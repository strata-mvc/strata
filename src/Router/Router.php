<?php

namespace Strata\Router;

use Exception;

use Strata\Strata;
use Strata\Router\RouteParser\Url\UrlRouter;
use Strata\Router\RouteParser\Callback\CallbackRouter;

/**
 * Assigns callback handlers based on the possible different contexts.
 * @link    http://strata.francoisfaubert.com/docs/routes/
 */
class Router
{
    /**
     * Generates a dynamic and unique callback ready to use with Wordpress'
     * add_action or add_filter calls.
     * @param string $ctrl   Controller class short name
     * @param string $action
     * @return array A valid callback for call_user_func
     */
    public static function callback($ctrl, $action)
    {
        return CallbackRouter::factory($ctrl, $action);
    }

    /**
     * Generates a parser for URL based rules, as one may be used to in
     * the world of Model View Controller programming.
     * @param  array $routes A list of available routes and callbacks
     * @return AltoRouter  The parser that generates a valid route
     */
    public static function urlRouting($routes = array())
    {
        return UrlRouter::factory($routes);
    }

    /**
     * Returns whether the current request is considered being
     * called as an Ajax query.
     * @return boolean
     */
    public static function isAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    /**
     * Attempts to decide if the current Ajax request is happening on the frontend
     * instead of the backend.
     * @return boolean
     * @link https://snippets.khromov.se/determine-if-wordpress-ajax-request-is-a-backend-of-frontend-request/
     */
    public static function isFrontendAjax()
    {
        if (self::isAjax()) {
            $referer = '';
            $filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';

            if (!empty($_REQUEST['_wp_http_referer'])) {
                $referer = wp_unslash($_REQUEST['_wp_http_referer']);
            } elseif (!empty($_SERVER['HTTP_REFERER'])) {
                $referer = wp_unslash($_SERVER['HTTP_REFERER']);
            }

            return strpos($referer, admin_url()) === false && basename($filename) === 'admin-ajax.php';
        }

        return false;
    }

    /**
     * @var Strata\Router\RouteParser\Route A route that this object will try to execute
     */
    public $route = null;

    /**
     * Attempts to run the currently loaded route object.
     * @return mixed Returns what the action function will have returned.
     * @throws  Exception when the route is not instantiated.
     * @filter strata_on_before_url_routing
     */
    public function run($url = null)
    {
        if (is_null($this->route)) {
            throw new Exception("This is an invalid route.");
        }

        // Allow plugins and the code outside the MVC system to cancel
        // a route.
        if (function_exists('apply_filters')) {
            $url = apply_filters('strata_on_before_url_routing', $url);
        }

        $this->route->process($url);

        if ($this->route->isValid()) {
            return $this->loopCurrentRequest();
        }

        $controllerClass = get_class($this->route->controller);
        $messagePattern = "<warning>%s#%s is not a matched Strata route.</warning>";
        $this->log(sprintf($messagePattern, $controllerClass, $this->route->action));
    }

    /**
     * Abandons the current route.
     */
    public function abandonCurrent()
    {
        $this->route->cancel();
    }

    /**
     * Returns the current controller object.
     * @return Strata\Controller\Controller
     */
    public function getCurrentController()
    {
        if ($this->route->isValid()) {
            return $this->route->controller;
        }
    }

    /**
     * Returns the current action.
     * @return string
     */
    public function getCurrentAction()
    {
        if ($this->route->isValid()) {
            return $this->route->action;
        }
    }

    /**
     * While the route is not completed or canceled,
     * executes the route.
     * @return mixed Whatever is being returned by the function
     */
    private function loopCurrentRequest()
    {
        while (!$this->route->isCancelled()) {
            $this->route->start();

            $this->route->controller->init();
            $this->route->controller->before();

            $returnData = call_user_func_array(array($this->route->controller, $this->route->action), $this->route->arguments);

            $this->route->controller->after();
            $this->route->end();

            return $returnData;
        }
    }

    /**
     * Sends a message to the global logger.
     * @param  string $msg
     * @param  string $type
     */
    private function log($msg, $type = "<success>Router</success>")
    {
        Strata::app()->log($msg, $type);
    }
}
