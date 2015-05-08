<?php
namespace Strata\Router\RouteParser\Callback;

use Strata\Router\Router;
use Strata\Router\RouteParser\Callback\CallbackRouteMatch;

class CallbackRouter extends Router
{
    public static function factory($ctrl, $action)
    {
        $router = new self();
        return $router->generate($ctrl, $action);
    }

    /**
     * Catch-all for static methods that allows just in time understanding of
     * dynamic callbacks. This is required because function like add_submenu_page
     * do not allow you to send arguments to a callback. (See EntityTable::addAdminMenus)
     * @param string $destinationPattern A pattern formatted like ___dynamic___callback___ControllerClass___Action
     * @param mixed $args Optional
     */
    public function __call($methodPattern, $args = array())
    {
        $this->route = new CallbackRoute();
        $this->route->addPossibilities($methodPattern);
        $this->route->arguments = $args;

        return $this->run();
    }

    public function generate($controllerName, $action)
    {
        $uniqueActionName = $this->_generateUniqueActionName($action);
        $methodName = sprintf('___dynamic___callback___%s___%s', $controllerName, $uniqueActionName);

        return array($this, $methodName);
    }

    /**
     * Generates a unique dynamic method name in the router. Takes a base
     * action name and works much like slugs do.
     * @param  string The original action name
     * @return string Unique action method name
     */
    private function _generateUniqueActionName($action)
    {
        $count = 0;
        while(method_exists($this, $action))  {
            $action = $action . "___" . $count++;
        }
        return $action;
    }
}
