<?php

namespace Strata\Controller;

use Strata\Strata;
use Strata\Utility\Hash;

/**
 * Allows the automation of Helpers loading.
 */
class HelperLoader {
    /**
     * A Strata Controller instance to which shortcodes callbacks will be forwarded
     * @var null
     */
    private $controller = null;
    private $helpers = null;

    public function __construct(\Strata\Controller\Controller $controller)
    {
        if (is_null($controller)) {
            throw new Exception("No controller has been defined in which helpers can be instantiated.");
        }

        $this->controller = $controller;
        $this->helpers = Hash::normalize($this->controller->helpers);
    }

    /**
     * Specifies if a number of helpers have been defined.
     * @return boolean True if some are present.
     */
    public function hasHelpers()
    {
        return count($this->_helpers) > 0;
    }

    /**
     * Registers helpers
     * @return  null
     */
    public function register()
    {
        if ($this->hasHelpers()) {
            $this->log();
            foreach ($this->helpers as $helper => $config) {
                $this->controller->view->loadHelper($helper, $config);
            }
        }
    }

    private function log()
    {
        $app = Strata\Strata::app();
        $app->log(sprintf("Autoloaded %s view helpers: %s", count($this->helpers), implode(", ", $this->helpers)));
    }
}
