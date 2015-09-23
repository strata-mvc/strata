<?php
namespace Strata\View\Helper;

use Strata\Strata;
use Strata\Utility\Inflector;
use Exception;
use ReflectionClass;

/**
 * A base class for ViewHelper objects
 */
class Helper {

    /**
     *
     * @param  string $name The class name of the helper
     * @param   mixed $config Optional helper configuration
     * @return mixed       A controller
     */
    public static function factory($name, $config = array())
    {
        // Check for custom validators in the Strata scope as well as in
        // the project scope. Project scope has priority.
        $scopes = array(
            self::generateClassPath($name),
            self::generateClassPath($name, false)
        );

        foreach ($scopes as $HelperName) {
            if (class_exists($HelperName)) {
                return new $HelperName($config);
            }
        }

        throw new Exception("Strata : No file matched the view helper '$classpath'.");
    }

    /**
     * Generates a possible namespace and classname combination of a
     * Strata view helper. Mainly used to avoid hardcoding the '\\View\\Helper\\'
     * string everywhere.
     * @param  string $name The class name of the helper
     * @param  boolean $local Generated a path that is relative to the current project. Default to false.
     * @return string       A fulle namespaced view helper name
     */
    public static function generateClassPath($name, $local = true)
    {
        $namespace = $local ? Strata::getNamespace() : 'Strata';
        return $namespace . "\\View\\Helper\\" . self::generateClassName($name);
    }

    public static function generateClassName($name)
    {
        $name = str_replace("-", "_", $name);
        $name = Inflector::underscore($name);
        $name = Inflector::classify($name);

        if (!preg_match("/Helper$/", $name)) {
            $name .= "Helper";
        }

        return $name;
    }

    public function getShortName()
    {
        $rc = new ReflectionClass($this);
        return $rc->getShortName();
    }

}
