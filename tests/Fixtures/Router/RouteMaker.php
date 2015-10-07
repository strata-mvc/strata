<?php
namespace Tests\Fixtures\Router;

use Strata\Router\Router;
use Tests\Fixtures\Router\FakeRoute;
use Tests\Fixtures\Controller\TestController;

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
