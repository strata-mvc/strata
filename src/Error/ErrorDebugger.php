<?php

namespace Strata\Error;

use Strata\Strata;
use Strata\View\Template;

/**
 * The object that helps representing the errors visually.
 */
class ErrorDebugger extends Template
{
    public function setErrorData($error)
    {
        $this->updateCommonConfiguration();
        $this->setViewName("error");
        $this->injectVariables(array("error" => $error));
    }

    protected function updateCommonConfiguration()
    {
        $this->setConfig("view_source_path", Strata::getOurVendorPath() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Error' . DIRECTORY_SEPARATOR);
        $this->setConfig("use_localized_views", false);
        $this->setConfig("allow_debug", false);
        $this->setConfig("layout", "layout");
    }
}
