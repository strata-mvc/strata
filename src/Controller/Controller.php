<?php

namespace Strata\Controller;

use Strata\Controller\Request;
use Strata\Controller\ShortcodeLoader;
use Strata\View\View;
use Strata\Strata;
use Exception;

/**
 * Base controller class.
 */
class Controller {

    /**
     *
     * @param  string $name The class name of the controller
     * @return mixed       A controller
     */
    public static function factory($name)
    {
        $classpath = self::generateClassPath($name);
        if (class_exists($classpath)) {
            return new $classpath();
        }

        throw new Exception("Strata : No file matched the controller '$classpath'.");
    }

    /**
     * Generates a possible namespace and classname combination of a
     * Strata controller. Mainly used to avoid hardcoding the '\\Controller\\'
     * string everywhere.
     * @param  string $name The class name of the controller
     * @return string       A fully namespaced controller name
     */
    public static function generateClassPath($name)
    {
        return Strata::getNamespace() . "\\Controller\\" . ucfirst($name);
    }

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
        $this->request = new Request();
        $this->view = new View();

        $shortcodes = new ShortcodeLoader($this);
        $shortcodes->register();
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
}
