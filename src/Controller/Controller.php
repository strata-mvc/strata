<?php

namespace Strata\Controller;

use Strata\Controller\Request;
use Strata\Controller\Loader\ShortcodeLoader;
use Strata\Controller\Loader\HelperLoader;
use Strata\Core\StrataObjectTrait;
use Strata\View\View;

/**
 * Base controller class.
 * @link http://strata.francoisfaubert.com/docs/controllers/
 */
class Controller
{
    use StrataObjectTrait;

    /**
     * {@inheritdoc}
     */
    public static function getNamespaceStringInStrata()
    {
        return "Controller";
    }

    /**
     * {@inheritdoc}
     */
    public static function getClassNameSuffix()
    {
        return "Controller";
    }

    /**
     * The current active Request
     * @var Request
     */
    public $request = null;

    /**
     * The associated view template
     * @var View
     */
    public $view = null;

    /**
     * These hooks allow views to use Wordpress nicely, but still trigger
     * items in the current controller.
     * @link https://codex.wordpress.org/Shortcode_API
     * @var array
     */
    public $shortcodes = array();

    /**
     * Helpers that will need to be loaded across all the actions of the Controller.
     * @var array
     */
    public $helpers = array();

    /**
     * Initiates the Controller object by setting up the Request, the associated
     * View and the various autoloaders.
     * @return null
     */
    public function init()
    {
        $this->request = new Request();
        $this->view = new View();

        $shortcodes = new ShortcodeLoader($this);
        $shortcodes->register();

        $helpers = new HelperLoader($this);
        $helpers->register();
    }

    /**
     * Executed after each call to a controller action.
     * @return null
     */
    public function after()
    {

    }

    /**
     * Executed before each call to a controller action.
     * @return null
     */
    public function before()
    {

    }

    /**
     * Base entry action.
     * @return  null
     */
    public function index()
    {

    }

    /**
     * Base action when no action is found. This is used mainly as a precautionary
     * fallback when a route matches a controller but not a method.
     * @return  null
     */
    public function noActionMatch()
    {

    }
}
