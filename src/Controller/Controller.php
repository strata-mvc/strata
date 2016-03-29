<?php

namespace Strata\Controller;

use Strata\Strata;
use Strata\Router\Router;
use Strata\Router\RouteParser\Url\UrlRoute;
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

    public function notFound()
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 400);

        global $wp_query;
        $wp_query->set_404();
    }

    public function serverError()
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    }


    public function redirect($controllerName, $action = "index", $arguments = array())
    {
        $view = $this->view;
        $request = $this->request;

        $router = Strata::router();
        if (is_null($router)) {
            return;
        }

        $router->abandonCurrent();

        $route = new UrlRoute();
        $route->controller = Controller::factory($controllerName);
        $route->controller->view = $view;
        $route->controller->request = $request;
        $route->action = $action;
        $route->arguments = $arguments;

        return $route->attemptCompletion();
    }
}
