<?php

namespace Strata\Controller;

use Strata\Controller\Request;
use Strata\View\View;

/**
 * Base controller class.
 */
class Controller {

    /**
     * The current request
     *
     * @var Strata\Controller\Request
     */
    public $request = null;

    /**
     * The associated view template
     *
     * @var Strata\View\View
     */
    public $view = null;

    /**
     * These hooks allow views to use Wordpress nicely, but still trigger
     * items in the current controller.
     *
     * @var  array
     */
    public $shortcodes = array();

    /**
     * Initiate the controller.
     * @return null
     */
    public function init()
    {
        // Save the current request
        $this->request = new Request();
        $this->view = new View();

        // If this controller has shortcodes, try to assign them.
        $this->_buildShortcodes();
    }

    /**
     * Executed after each calls to a controller action.
     * @return null
     */
    public function after()
    {

    }

    /**
     * Executed before each calls to a controller action.
     * @return null
     */
    public function before()
    {

    }

    /**
     * Base action. This is used mainly as a precautionary fallback.
     * @return  null
     */
    public function index()
    {

    }

    /**
     * Registers dynamic shortcodes hooks to the instantiated controller.
     * Note that these are not available when this instance of the controller
     * is not being loaded.
     * @return  null
     */
    protected function _buildShortcodes()
    {
        if (count($this->shortcodes) > 0) {
            foreach ($this->shortcodes as $shortcode => $methodName) {
                if(method_exists($this, $methodName)) {
                    add_shortcode($shortcode, array($this, $methodName));
                }
            }
        }
    }
}
