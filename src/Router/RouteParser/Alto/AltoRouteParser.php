<?php

namespace Strata\Router\RouteParser\Alto;

use Strata\Router\Router;
use Strata\Router\RouteParser\Alto\AltoRoute;

/**
 * Maps Wordpress urls to Strata classes
 * @link    http://strata.francoisfaubert.com/docs/routes/
 */
class AltoRouteParser extends Router
{
    /**
     * Returns an instance of the router instantiated
     * with the optional $routes.
     * @param  array  $routes (optional)
     * @return AltoRouteParser
     */
    public static function factory($routes = array())
    {
        $router = new self();
        if (count($routes)) {
            $router->addRoutes($routes);
        }
        return $router;
    }

    /**
     * @var boolean Flag used to prevent routes from being registered twice.
     */
    private $registered = false;

    function __construct()
    {
        $this->route = new AltoRoute();
    }

    /**
     * Adds possibles routes the router instance
     * @param array $routes
     */
    public function addRoutes($routes = array())
    {
        if (!$this->isRegistered()) {
            $this->registerWordpressAction();
        }

        $this->route->addPossibilities($routes);
    }

    /**
     * Registers the Wordpress action required to
     * handle the routing at the correct timing.
     */
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

    /**
     * Returns whether the Wordpress event has
     * already been added.
     * @return boolean
     */
    protected function isRegistered()
    {
        return (bool)$this->registered;
    }

    /**
     * The callback sent to Wordpress' 'wp' action. It understands the current
     * url context and calls the current controller's method, if applicable.
     */
    public function onWordpressInit()
    {
        $this->run();
    }

    /**
     * The callback sent to Wordpress' 'init' action. It understands the current
     * url context and calls the current controller's method, if applicable.
     */
    public function onWordpressEarlyInit()
    {
        $this->onWordpressInit();
    }
}
