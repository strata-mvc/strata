<?php
namespace Strata\Context;

use Strata\Router;
use Strata\Utility\Hash;

class RoutingContext {

  /**
     * Catch-all for static methods that allows just in time understanding of
     * dynamic callbacks. This is required because function like add_submenu_page
     * do not allow you to send arguments to a callback. (See EntityTable::addAdminMenus)
     * @param string $method
     * @param mixed $args Optional
     */
    public static function __callStatic($method, $args)
    {
        if (preg_match('/^___dynamic___callback___(.+)___(.+)(___\d+)?/', $method, $matches)) {
            $app = \Strata\Strata::app();
            $className = $app->getNamespace() . "\\Controller\\" . $matches[1];
            return Router::performAction($className, $matches[2], $args);
        }
    }

    /**
     * Generate a dynamic and unique callback ready to use with wordpress' add_action calls.
     * @param string $ctrl Controller class shortname
     * @param string $action
     * @return array A valid callback for call_user_func
     */
    public static function callback($ctrl, $action)
    {
        $count = 0;
        while(method_exists(__CLASS__, $action))  {
            $action = $action . "___" . $count++;
        }
        return array('Strata\Router', sprintf('___dynamic___callback___%s___%s', $ctrl, $action));
    }

    /**
     * Starts the routing process based on the app's predefined configuration.
     */
    public static function kickstart()
    {
        $app = \Strata\Strata::app();
        $routes = \Strata\Strata::config('routes');

        // If routes are not present, router has nothing to do and processing should be ignored.
        if ($app && $routes) {
            $router = new \Strata\Router();
            $router->init();

            // Hook on Wordpress' init action to start any process.
            add_action('init' , array($router, "onWordpressInit"));
        }
    }

    /**
     * Executes a complete calls to the Controller object, including controller callbacks and shortcodes generation.
     */
    public static function performAction($className, $methodName, $params = array())
    {
        if(method_exists($className, $methodName))  {
            $ctrl = new $className();
            $ctrl->init();

            call_user_func(array($ctrl, "before"));
            $returnData = call_user_func_array(array($ctrl, $methodName), $params);
            call_user_func(array($ctrl, "after"));

            return $returnData;
        }
    }
}
