<?php

use Strata\Router\Router;
use Strata\Router\RouteParser\Url\UrlRouter;

use Test\Fixture\Controller\TestController;
use Test\Fixture\Router\RouteMaker;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public $callback;
    public $url;
    public $wordpress;

    public function setUp()
    {
        $this->wordpress = wordpress();
        $this->wordpress->reset();

        $this->callback = Router::callback("TestController", "returnTrue");
        $this->url = Router::urlRouting(array(
            array("GET", "/test1/", "TestController#returnTrue")
        ));
        $this->url->route->listen();

    }

    public function testCanBeInstanciated()
    {
        $this->assertTrue(new Router() instanceof Router);
    }

    /**
     * @expectedException        Exception
     */
    public function testInvalidCallbackDestination()
    {
        $invalidCallback = Router::callback("InvalidController", "invalid");
        call_user_func($invalidCallback);
    }

    public function testCanGenerateACallback()
    {
        $this->assertTrue(is_array($this->callback));
        $this->assertCount(2, $this->callback);
    }

    public function testGeneratesARouter()
    {
        $this->assertTrue($this->callback[0] instanceof Router);
    }

    public function testGeneratesAnExecutableRoute()
    {
        $this->assertTrue(call_user_func($this->callback));
    }

    public function testCanGenerateUrlRoutes()
    {
        $this->assertTrue($this->url instanceof UrlRouter);
    }

    public function testGeneratesAWordpressHook()
    {
        $this->assertArrayHasKey('init', $this->wordpress->actions);
        $this->assertEquals('onWordpressEarlyInit', $this->wordpress->actions['init'][0][1]);
    }

    public function testUrlRoutesCanRun()
    {
        $this->url->run();
    }

    public function testExecutesInCorrectOrder()
    {
        $router = RouteMaker::routeToTest();
        $router->run();

        $this->assertEquals(array('before', 'fn', 'after'), $router->route->controller->stackorder);
    }
}
