<?php

namespace Strata\Security;

use Strata\Strata;
use Strata\Security\ImprovementBase;

/**
 * Sets a default timezone value in the current PHP process.
 */
class TimezoneSetter extends ImprovementBase
{
    const DEFAULT_TIMEZONE = 'America/New_York';

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $message = "PHP timezone set to %s";
        $timezone = Strata::app()->getConfig("timezone");

        if (is_null($timezone)) {
            $timezone = self::DEFAULT_TIMEZONE;
            $message = "PHP timezone automatically set to %s";
        }

        date_default_timezone_set($timezone);
        Strata::app()->log(sprintf($message, $timezone));
    }
}
