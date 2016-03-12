<?php

namespace Strata\Logger;

use Strata\Strata;

class ConsoleLogger extends LoggerBase
{
    public function initialize()
    {
        $this->setConfig("output", "php://stderr");
        parent::initialize();
    }

    public function write($context, $message)
    {
        $date = date('[D M m H:i:s Y]');
        $handle = fopen($this->getConfig("output"), "w+");
        fwrite($handle, sprintf("%s %s %s", $date, $this->format($context), $this->format($message) . "\n"));
        fclose($handle);
    }

    public function writeNl()
    {
        $handle = fopen($this->getConfig("output"), "w+");
        fwrite($handle, "\n");
        fclose($handle);
    }
}
