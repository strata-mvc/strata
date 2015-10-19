<?php
namespace Test\Fixture\Router;

use Strata\Router\Router;
use Test\Fixture\Router\FakeRoute;
use Test\Fixture\Controller\TestController;

class RouteMaker
{

    public static function routeToTest()
    {
        $route = new FakeRoute();
        $route->controller = new TestController();
        $route->action = "returnTrue";

        $router = new Router();
        $router->route = $route;

        return $router;
    }
}
