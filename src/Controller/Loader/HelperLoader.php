<?php

namespace Strata\Controller\Loader;

use Strata\Controller\Controller;
use Strata\Strata;
use Strata\Utility\Hash;
use Exception;

/**
 * Allows the automation of Helpers loading on a specified Controller object.
 * @link http://strata.francoisfaubert.com/docs/controllers/
 */
class HelperLoader
{
    /**
     * The Strata Controller instance on which helpers will be loaded.
     * @var Controller;
     */
    private $controller = null;

    /**
     * A list of normalized helper names and configuration.
     * @var array
     */
    private $helpers = array();

    /**
     * Helper loader constructor builds a list of helper objects
     * associated to a controller and instantiates them as view
     * variables.
     * @param Controller $controller
     */
    public function __construct(Controller $controller)
    {
        if (is_null($controller)) {
            throw new Exception("No controller has been defined in which helpers can be instantiated.");
        }

        $this->controller = $controller;
        $this->helpers = $this->getNormalizedHelpers();
    }

    /**
     * Registers the controller's helpers within it's active view.
     * @return null
     */
    public function register()
    {
        if ($this->hasHelpers()) {
            foreach ($this->helpers as $helper => $config) {
                $this->controller->view->loadHelper($helper, $config);
            }

            $this->logAvailableHelpers();
        }
    }

    /**
     * Specifies if a quantity of helpers have been defined by the controller.
     * @return boolean true when helpers are found
     */
    protected function hasHelpers()
    {
        return count((array)$this->helpers) > 0;
    }

    /**
     * Returns a normalized array of the controller's helper names and configuration.
     * @return array
     */
    protected function getNormalizedHelpers()
    {
        if (isset($this->controller->helpers)) {
            return Hash::normalize((array)$this->controller->helpers);
        }

        return array();
    }

    /**
     * Logs the list of helpers the loader has attempted to register.
     * @return null
     */
    private function logAvailableHelpers()
    {
        $names = array_keys($this->helpers);
        $app = Strata::app();
        $app->setConfig("runtime.helpers", $names);
    }
}
