<?php
namespace Strata\Router\RouteParser\Alto;

use Strata\Router\Router;
use Strata\Router\RouteParser\Alto\AltoRoute;

/**
 * Maps wordpress urls to Strata classes
 *
 * @package       Strata.Router
 * @link          http://strata.francoisfaubert.com/docs/routes/
 */
class AltoRouteParser extends Router {

    public static function factory($routes = array())
    {
        $router = new self();
        if (count($routes)) {
            $router->addRoutes($routes);
            $router->registerWordpressAction();
        }
        return $router;
    }

    function __construct()
    {
        $this->route = new AltoRoute();
    }

    /**
     * Configures the router instance
     */
    public function addRoutes($routes = array())
    {
        $this->route->addPossibilities($routes);
    }

    protected function registerWordpressAction()
    {
        add_action('init' , array($this, "onWordpressInit"));
    }

    /**
     * The callback sent to Wordpress' 'init' action. It understands the current
     * url context and calls the current controller's method, if applicable.
     */
    public function onWordpressInit()
    {
        $this->run();
    }
}


