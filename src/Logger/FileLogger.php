<?php

namespace Strata\Logger;

use Strata\Strata;

class FileLogger extends LoggerBase
{
    public function initialize()
    {
        $this->setConfig("output", Strata::getLogPath() . "strata.log");
        $this->setConfig("outputAs", LoggerBase::PLAIN);
    }

    public function write($context, $message)
    {
        $handle = fopen($this->getConfig("output"), "w+");
        fwrite($handle, sprintf("[%s] %s", $context, $message));
        fclose($handle);
    }

    public function writeNl()
    {
        $handle = fopen($this->getConfig("output"), "w+");
        fwrite($handle, "\n");
        fclose($handle);
    }
}
