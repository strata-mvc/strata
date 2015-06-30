<?php
namespace Strata\Logger;

use Strata\Strata;

/**
 * Logs strings
 *
 * @package       Strata.Logger
 */
class Logger {

    public $logfile;
    public $color = "\e[0;36m";

    function __construct()
    {
        $this->logfile = Strata::getLogPath() . "strata.log";
    }

    public function log($message, $context = "[Strata::Log]")
    {
        if (WP_ENV == 'development') {
            error_log($this->buildSimpleLine($context, $message), 3, $this->logfile);
            error_log($this->buildRichLine($context, $message));
        }
    }

    public function debug($message, $context = "[Strata::Debug]")
    {
        if (WP_ENV == 'development') {
            error_log($this->buildSimpleLine($context, $message), 3, $this->logfile);
            error_log($this->buildRichLine($context, $message));
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


