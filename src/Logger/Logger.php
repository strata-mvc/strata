<?php

namespace Strata\Logger;

use Strata\Strata;

/**
 * Log messages to the application's log file.
 */
class Logger
{
    /**
     * @var string The full path to the log file used for output
     */
    private $logfile;

    /**
     * The log context are colorized in the output to make them easier to find.
     * This value is expected to be a valid bash color character.
     * @var string
     */
    public $color = "\e[0;36m";

    function __construct()
    {
        $this->setLogFile();
    }

    /**
     * Defines and creates the logger's log file.
     * @param string $name (optional) A file name
     */
    public function setLogFile($name = 'strata.log')
    {
        $this->logfile = Strata::getLogPath() . $name;

        if (!file_exists($this->logfile)) {
            @touch($this->logfile);
        }
    }

    /**
     * Sends a message of type log
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     */
    public function log($message, $context = "[Strata:Log]")
    {
        if ($this->canPrintLogs()) {
            $this->write($message, $context);
        }
    }

    /**
     * Sends a message of type debug
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     */
    public function debug($message, $context = "[Strata:Debug]")
    {
        $this->log($message, $context);
    }

    /**
     * Sends a message of type debug
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     */
    public function error($message, $context = "[Strata:Error]")
    {
        $originalColor = $this->color;
        $this->color = "\e[0;31m";
        $this->log($message, $context);
        $this->color = $originalColor;
    }

    /**
     * Specifies whether the application should generate logs.
     * @return boolean
     */
    protected function canPrintLogs()
    {
        return Strata::isDev() && is_writable($this->logfile);
    }

    /**
     * Writes the message to the log file. If the application is
     * running as part of the bundled server print on screen.
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     */
    protected function write($message, $context)
    {
        file_put_contents($this->logfile, $this->buildSimpleLine($message, $context), FILE_APPEND | LOCK_EX);

        if (Strata::isBundledServer()) {
            error_log($this->buildRichLine($message, $context), 4);
        }
    }

    /**
     * Builds a line that contains color.
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     * @return string
     */
    protected function buildRichLine($message, $context)
    {
        return sprintf("%s%s\e[0m %s", $this->color, $context, $message);
    }

    /**
     * Builds a line that is not preformatted.
     * @param  string $message
     * @param  string $context (optional) Flag to separate message types
     * @return string
     */
    protected function buildSimpleLine($message, $context)
    {
        return sprintf("%s %s\n", $context, $message);
    }
}
