<?php
namespace Tests\Fixtures\Router;

use Strata\Router\Router;
use Strata\Router\RouteParser\Route;
use Tests\Fixtures\Controller\TestController;

class RouteMaker {

    public static function routeToTest()
    {
        $route = new Route();
        $route->controller = new TestController();
        $route->action = "returnTrue";

        $router = new Router();
        $router->route = $route;

        return $router;
    }

}
