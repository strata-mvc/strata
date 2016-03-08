<?php

namespace Strata\Error;

use Strata\Strata;
use Exception;

/**
 * The object that helps representing the errors visually in the logs.
 */
class ErrorLogger
{
    public function logError(array $error)
    {
        $this->logMessage(sprintf(
            '%s (%s): %s in [%s, line %s]',
            $error['type'],
            $error['code'],
            $error['description'],
            $error['file'],
            $error['line']
        ));
    }

    private function logMessage($message = "")
    {
        $logger = Strata::app()->getLogger();
        $logger->error($message, "[Strata:ErrorLogger]");
    }
}
