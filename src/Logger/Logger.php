<?php
namespace Strata\Logger;

use Strata\Strata;

/**
 * Logs strings
 *
 * @package Strata.Logger
 */
class Logger
{

    public $logfile;
    public $color = "\e[0;36m";

    function __construct()
    {
        $this->logfile = Strata::getLogPath() . "strata.log";
        if (!file_exists($this->logfile)) {
            @touch($this->logfile);
        }
    }

    public function log($message, $context = "[Strata:Log]")
    {
        if ($this->canPrintLogs()) {
            $this->write($message, $context);
        }
    }

    public function debug($message, $context = "[Strata:Debug]")
    {
        if ($this->canPrintLogs()) {
            $this->write($message, $context);
        }
    }

    protected function canPrintLogs()
    {
        return Strata::isDev() && is_writable($this->logfile);
    }

    protected function write($message, $context)
    {
        file_put_contents($this->logfile, $this->buildSimpleLine($context, $message), FILE_APPEND | LOCK_EX);

        if (Strata::isBundledServer()) {
            error_log($this->buildRichLine($context, $message), 4);
        }
    }

    protected function buildRichLine($context, $message)
    {
        return sprintf("%s%s\e[0m %s", $this->color, $context, $message);
    }

    protected function buildSimpleLine($context, $message)
    {
        return sprintf("%s %s\n", $context, $message);
    }
}


