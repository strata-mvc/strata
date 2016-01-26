<?php
namespace Strata\Router\RouteParser\Alto;

use Strata\Router\Router;
use Strata\Router\RouteParser\Alto\AltoRoute;

/**
 * Maps wordpress urls to Strata classes
 *
 * @package Strata.Router
 * @link    http://strata.francoisfaubert.com/docs/routes/
 */
class AltoRouteParser extends Router
{

    public static function factory($routes = array())
    {
        $router = new self();
        if (count($routes)) {
            $router->addRoutes($routes);
        }
        return $router;
    }

    private $registered = false;

    function __construct()
    {
        $this->route = new AltoRoute();
    }

    /**
     * Configures the router instance
     */
    public function addRoutes($routes = array())
    {
        if (!$this->isRegistered()) {
            $this->registerWordpressAction();
        }

        $this->route->addPossibilities($routes);
    }

    protected function registerWordpressAction()
    {
        if (function_exists('add_action')) {
            if (Router::isAjax() || is_admin()) {
                add_action('init', array($this, "onWordpressEarlyInit"));
            } else {
                add_action('wp', array($this, "onWordpressInit"));
            }
        }

        $this->registered = true;
    }

    protected function isRegistered()
    {
        return (bool)$this->registered;
    }

    /**
     * The callback sent to Wordpress' 'init' action. It understands the current
     * url context and calls the current controller's method, if applicable.
     */
    public function onWordpressInit()
    {
        $this->run();
    }

    public function onWordpressEarlyInit()
    {
        $this->onWordpressInit();
    }
}
