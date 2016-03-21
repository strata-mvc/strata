<?php

namespace Strata\Logger;

use Strata\Strata;
use ReflectionObject;
use ReflectionProperty;

/**
 */
class Debugger
{
    const RAW = 1;
    const CONSOLE = 2;
    const HTML = 3;
    const HTML_STYLES = "font-size: 12px; font-family: consolas; background:ccc; color:#000;";

    public static function trace($backtrace = null, $options = array())
    {
        if (is_null($backtrace)) {
            $backtrace = debug_backtrace();
        }
        $backtrace = array_reverse($backtrace);

        $count = count($backtrace);
        $options += array(
            'depth' => 50,
            'start' => 0,
            'output' => static::HTML
        );

        switch ($options['output']) {
            case static::RAW :
                $tpl = "%s(%s) from of %s#%d\n"; break;
            case static::CONSOLE :
                $tpl = "<info>%s(%s)</info> in %s @ <yellow>%d</yellow>\n"; break;
            default :
                $tpl = "<div style=\"".static::HTML_STYLES."\"><strong>%s<em>(%s)</em></strong><br/>in %s @ %d<br/><br/></div>";
        }

        $trace = "";
        $i = $options['start'];
        while ($i < $count && $i < $options['depth']) {
            $details = $backtrace[$i];
            $file = isset($details['file']) ? str_replace(Strata::getRootPath(), '~', $details['file']) : 'unknown';
            $line = isset($details['line']) ? $details['line'] : 'unknown';

            $args = $details['args'];
            $arguments = array();
            // foreach ($args as $arg) {
            //     $arguments[] = static::getType($arg);
            // }

            $trace .= sprintf($tpl, $details['function'], implode(", ", $arguments), $file, $line);
            $i++;
        }

        return $trace;
    }

  /**
     * Export function used to keep track of indentation and recursion.
     *
     * @param mixed $var The variable to dump.
     * @param int $depth The remaining depth.
     * @param int $indent The current indentation level.
     * @return string The dumped variable.
     * @link https://github.com/cakephp/cakephp/blob/master/src/Error/Debugger.php
     */
    public static function export($var, $depth = 1, $indent = 0)
    {
        switch (static::getType($var)) {
            case 'boolean':
                return ($var) ? 'true' : 'false';
            case 'integer':
                return '(int) ' . $var;
            case 'float':
                return '(float) ' . $var;
            case 'string':
                if (trim($var) === '') {
                    return "''";
                }
                return "'" . $var . "'";
            case 'array':
                return static::exportArray($var, $depth - 1, $indent + 1);
            case 'resource':
                return strtolower(gettype($var));
            case 'null':
                return 'null';
            case 'unknown':
                return 'unknown';
            default:
                return static::exportObject($var, $depth - 1, $indent + 1);
        }
    }

    /**
     * Get the type of the given variable. Will return the class name
     * for objects.
     *
     * @param mixed $var The variable to get the type of.
     * @return string The type of variable.
     */
    public static function getType($var)
    {
        if (is_object($var)) {
            return get_class($var);
        }
        if ($var === null) {
            return 'null';
        }
        if (is_string($var)) {
            return 'string';
        }
        if (is_array($var)) {
            return 'array';
        }
        if (is_int($var)) {
            return 'integer';
        }
        if (is_bool($var)) {
            return 'boolean';
        }
        if (is_float($var)) {
            return 'float';
        }
        if (is_resource($var)) {
            return 'resource';
        }
        return 'unknown';
    }


    /**
     * Export an array type object. Filters out keys used in datasource configuration.
     *
     * The following keys are replaced with ***'s
     *
     * - password
     * - login
     * - host
     * - database
     * - port
     * - prefix
     * - schema
     *
     * @param array $var The array to export.
     * @param int $depth The current depth, used for recursion tracking.
     * @param int $indent The current indentation level.
     * @return string Exported array.
     * @link https://github.com/cakephp/cakephp/blob/master/src/Error/Debugger.php
     */
    protected static function exportArray(array $var, $depth = 1, $indent = 0)
    {
        $out = "[";
        $break = $end = null;
        if (!empty($var)) {
            $break = "\n" . str_repeat("\t", $indent);
            $end = "\n" . str_repeat("\t", $indent - 1);
        }
        $vars = [];
        if ($depth >= 0) {
            foreach ($var as $key => $val) {
                // Sniff for globals as !== explodes in < 5.4
                if ($key === 'GLOBALS' && is_array($val) && isset($val['GLOBALS'])) {
                    $val = '[recursion]';
                } elseif ($val !== $var) {
                    $val = static::export($val, $depth, $indent);
                }
                $vars[] = $break . static::export($key) .
                    ' => ' .
                    $val;
            }
        } else {
            $vars[] = $break . '[maximum depth reached]';
        }
        return $out . implode(',', $vars) . $end . ']';
    }


    /**
     * Handles object to string conversion.
     *
     * @param string $var Object to convert.
     * @param int $depth The current depth, used for tracking recursion.
     * @param int $indent The current indentation level.
     * @return string
     * @link https://github.com/cakephp/cakephp/blob/master/src/Error/Debugger.php
     */
    protected static function exportObject($var, $depth = 1, $indent = 0)
    {
        $out = '';
        $props = [];
        $className = get_class($var);
        $out .= 'object(' . $className . ') {';
        $break = "\n" . str_repeat("\t", $indent);
        $end = "\n" . str_repeat("\t", $indent - 1);
        if ($depth > 0 && method_exists($var, '__debugInfo')) {
            try {
                return $out . "\n" .
                    substr(static::exportArray($var->__debugInfo(), $depth - 1, $indent), 1, -1) .
                    $end . '}';
            } catch (Exception $e) {
                $message = $e->getMessage();
                return $out . "\n(unable to export object: $message)\n }";
            }
        }
        if ($depth > 0) {
            $objectVars = get_object_vars($var);
            foreach ($objectVars as $key => $value) {
                $value = static::export($value, $depth - 1, $indent);
                $props[] = "$key => " . $value;
            }
            $ref = new ReflectionObject($var);
            $filters = [
                ReflectionProperty::IS_PROTECTED => 'protected',
                ReflectionProperty::IS_PRIVATE => 'private',
            ];
            foreach ($filters as $filter => $visibility) {
                $reflectionProperties = $ref->getProperties($filter);
                foreach ($reflectionProperties as $reflectionProperty) {
                    $reflectionProperty->setAccessible(true);
                    $property = $reflectionProperty->getValue($var);
                    $value = static::export($property, $depth - 1, $indent);
                    $key = $reflectionProperty->name;
                    $props[] = sprintf('[%s] %s => %s', $visibility, $key, $value);
                }
            }
            $out .= $break . implode($break, $props) . $end;
        }
        $out .= '}';
        return $out;
    }
}
