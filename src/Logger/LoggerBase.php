<?php

namespace Strata\Logger;

use Strata\Strata;
use Strata\Core\StrataConfigurableTrait;
use Strata\Core\StrataObjectTrait;

/**
 * Log messages to the application's log file.
 */
class LoggerBase
{
    use StrataConfigurableTrait;
    use StrataObjectTrait;

    const PLAIN = 1;
    const COLOR = 2;

    public $colors = array(
        'black' => 30,
        'red' => 91,
        'green' => 32,
        'yellow' => 33,
        'blue' => 34,
        'magenta' => 35,
        'cyan' => 36,
        'white' => 37
    );

    public $styles = array(
        'emergency' => ['text' => 'red'],
        'alert' => ['text' => 'red'],
        'critical' => ['text' => 'red'],
        'error' => ['text' => 'red'],
        'warning' => ['text' => 'yellow'],
        'info' => ['text' => 'cyan'],
        'debug' => ['text' => 'yellow'],
        'success' => ['text' => 'green'],
        'comment' => ['text' => 'blue'],
        'question' => ['text' => 'magenta'],
        'notice' => ['text' => 'cyan']
    );

    /**
     * The stack context helps carrying visual context
     * to the log by indenting the lines.
     * @var array
     */
    protected $stackContext = array();

    /**
     * Returns the default class name suffix for this object.
     * @return string
     */
    public static function getClassNameSuffix()
    {
        return "Logger";
    }

    public static function getNamespaceStringInStrata()
    {
        return "Logger";
    }

    public static function getFactoryScopes($name)
    {
        return array(
            self::generateClassPath($name),
            self::generateClassPath($name, false)
        );
    }

    public function initialize()
    {
        if ((DIRECTORY_SEPARATOR === '\\' && !(bool)getenv('ANSICON') && getenv('ConEmuANSI') !== 'ON') ||
            (function_exists('posix_isatty') && !posix_isatty($this->getConfig('output')))
        ) {
            $this->setConfig("outputAs", LoggerBase::PLAIN);
        } else {
            $this->setConfig("outputAs", LoggerBase::COLOR);
        }
    }

    /**
     * Sends a message of type log
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     */
    public function log($message, $context = "Strata:Log")
    {
        $this->write($context, $this->indent($message));
    }

    /**
     * Sends a message of type debug
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     */
    public function debug($message, $context = "Strata:Debug")
    {
        $this->write($context, $message);
    }

    /**
     * Sends a message of type debug
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     */
    public function error($message, $context = "Strata:Error")
    {
        $this->write('<critical>' . $context . '</critical>', '<critical>' . $message . '</critical>');
    }

    public function nl()
    {
        $this->writeNl();
    }

    public function logNewContext($message, $context =  "Strata:Log", $style = "black")
    {
        $this->write($context, $this->indent("┌─ ") . $message);
        $this->stackContext[] = $style;
    }

    public function logContextEnd($message, $context =  "Strata:Log", $style = "black")
    {
        array_pop($this->stackContext);
        $this->write($context, $this->indent("└─ ") . $message);
    }

    public function write($message, $context)
    {
        return false;
    }

    public function writeNl()
    {
        return false;
    }

    protected function format($message)
    {
        return $this->formatColor($this->formatType($message));
    }

    private function formatColor($text)
    {
        $tags = array_keys($this->styles) + array_keys($this->colors);
        if ($this->getConfig("outputAs") === LoggerBase::PLAIN) {
            return preg_replace('#</?(?:' . $tags . ')>#', '', $text);
        }

        return preg_replace_callback(
            '/<(?P<tag>[a-z0-9-_]+)>(?P<text>.*?)<\/(\1)>/ims',
            [$this, 'replaceTags'],
            $text
        );
    }

    protected function replaceTags($matches)
    {
        if (array_key_exists($matches['tag'], $this->colors)) {
            $frontColor = $this->colors[$matches['tag']];
            return "\033[" . $frontColor . 'm' . $matches['text'] . "\033[0m";
        }

        if (array_key_exists($matches['tag'], $this->styles)) {
            $styles = $this->styles[$matches['tag']];
            if (array_key_exists($styles['text'], $this->colors)) {
                $frontColor = $this->colors[$styles['text']];
                return "\033[" . $frontColor . 'm' . $matches['text'] . "\033[0m";
            }
        }

        return '<' . $matches['tag'] . '>' . $matches['text'] . '</' . $matches['tag'] . '>';
    }

    private function formatType($data)
    {
        if (is_string($data)) {
            return $data;
        }

        $object = is_object($data);
        if ($object && method_exists($data, '__toString')) {
            return (string)$data;
        }

        if ($object && $data instanceof JsonSerializable) {
            return json_encode($data);
        }

        return print_r($data, true);
    }

    private function indent($text)
    {
        if ($this->getConfig("outputAs") === LoggerBase::PLAIN) {
            return str_repeat("  ", count($this->stackContext)) . $text;
        }

        $str = "";
        foreach ($this->stackContext as $context) {
            $str .= "│ ";
        }

        return $str . $text;
    }
}
