<?php
namespace Strata\Router;

use Exception;

use Strata\Strata;
use Strata\Router\RouteParser\Alto\AltoRouteParser;
use Strata\Router\RouteParser\Callback\CallbackRouter;

/**
 * Assigns callback handlers on demand and from the URL context.
 *
 * @package Strata.Router
 * @link    http://strata.francoisfaubert.com/docs/routes/
 */
class Router
{

    /**
     * @var Strata\Router\RouteParser\Route A route that this object will try to execute
     */
    public $route = null;


    /**
     * Generates a dynamic and unique callback ready to use with Wordpress' add_action calls.
     * @param string $ctrl   Controller class shortname
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
     * @return Strata\Router\RouteParser\Alto\AltoRouteParser  The parser that generates a valid route
     */
    public static function urlRouting($routes = array())
    {
        return AltoRouteParser::factory($routes);
    }

    public static function isAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    // @thanks to https://snippets.khromov.se/determine-if-wordpress-ajax-request-is-a-backend-of-frontend-request/
    public static function isFrontendAjax()
    {

        if(self::isAjax()) {
              $referer = '';
              $filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';

            if (!empty($_REQUEST['_wp_http_referer'])) {
                $referer = wp_unslash($_REQUEST['_wp_http_referer']);
            } elseif(!empty($_SERVER['HTTP_REFERER'])) {
                $referer = wp_unslash($_SERVER['HTTP_REFERER']);
            }

              return strpos($referer, admin_url()) === false && basename($filename) === 'admin-ajax.php';
        }

        return false;
    }

    /**
     * Attemps to run the currently loaded route object.
     * @return mixed Returns what the action function will have returned.
     * @throws  Exception when the route is not instantiated.
     */
    public function run($url = null)
    {
        if (is_null($this->route)) {
            throw new Exception("This is an invalid route.");
        }

        // Allow plugins and code outside the MVC to cancel
        // a route.
        if (function_exists('apply_filters')) {
            $url = apply_filters('strata_on_before_url_routing', $url);
        }

        $this->route->process($url);

        if ($this->route->isValid()) {
            return $this->loopCurrentRequest();
        }

        $this->log(sprintf("%s#%s is not a matched Strata route.", get_class($this->route->controller), $this->route->action));
    }

    public function abandonCurrent()
    {
        $this->route->cancel();
    }

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

    private function log($msg, $type = "[Strata:Router]")
    {
        $app = Strata::app();
        $app->log($msg, $type);
    }
}


