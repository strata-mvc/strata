<?php

namespace Strata\Router\RouteParser\Callback;

use Strata\Router\Router;

/**
 * Maps Wordpress actions, filters and callbacks to Strata classes
 * @link    http://strata.francoisfaubert.com/docs/routes/
 */
class CallbackRouter extends Router
{
    /**
     * Returns an instance of the router instantiated
     * with the optional $routes.
     * @param  array  $routes (optional)
     * @return CallbackRoute
     */
    public static function factory($ctrl, $action)
    {
        $router = new self();
        return $router->generate($ctrl, $action);
    }

    /**
     * Generates a new dynamic route that can be passed as
     * a callable object.
     * @param  string $controllerName
     * @param  string $action
     * @return array                 A callable array
     */
    public function generate($controllerName, $action)
    {
        $this->route = new CallbackRoute();
        $this->route->addPossibilities(array($controllerName, $action));
        return array($this, "run");
    }

    /**
     * {@inheritdoc}
     */
    public function run($url = null)
    {
        $this->route->arguments = func_get_args();
        return parent::run();
    }
}
