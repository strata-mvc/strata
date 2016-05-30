<?php

namespace Strata\Router\RouteParser\Url;

use Strata\Strata;
use Strata\Router\Router;
use Strata\Router\RouteParser\Url\UrlRoute;

/**
 * Maps Wordpress urls to Strata classes
 * @link    http://strata.francoisfaubert.com/docs/routes/
 */
class UrlRouter extends Router
{
    /**
     * Returns an instance of the router instantiated
     * with the optional $routes.
     * @param  array  $routes (optional)
     * @return UrlRoute
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
        $this->route = new UrlRoute();
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
     * Adds possibles routes to the router instance
     * based on Custom Post Type information.
     * @param  \Strata\Model\WordpressEntity $model
     */
    public function addResource($model)
    {
        if (!$this->isRegistered()) {
            $this->registerWordpressAction();
        }

        $this->route->addResourcePossibility($model);
    }

    /**
     * Adds customized routes to the router instance that
     * have been defined in the custom post type's routing information.
     * @param array $routes
     */
    public function addModelRoutes($routes = array())
    {
        if (!$this->isRegistered()) {
            $this->registerWordpressAction();
        }

        $this->route->addModelPossibilities($routes);
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
                add_action('wp', array($this, "onWordpressInit"), 25);
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
        $this->route->listen();
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
