<?php

namespace Strata\Security;

use Strata\Strata;
use Strata\Security\ImprovementBase;

/**
 * Enforces triggers that warn browsers to be wary of XSS attacks.
 */
class XssHandler extends ImprovementBase
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        if ($this->shouldAdd()) {
            add_action('wp_head', array($this, "appendHeaderHtml"));
        }
    }

    /**
     * Checks the configuration file to ensure we are expected to
     * force Strata' xss protection.
     * @return boolean
     */
    protected function shouldAdd()
    {
        return !(bool)Strata::app()->getConfig("security.ignore_xss_validation");
    }

    /**
     * Appends HTML to the header that triggers additional browser security
     * @param  array $commentData
     * @return array
     */
    public function appendHeaderHtml($commentData)
    {

    }
}
