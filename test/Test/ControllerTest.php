<?php

use Test\Fixture\Router\RouteMaker;
use Test\Fixture\Controller\TestController;

use Strata\Controller\Controller;
use Strata\View\Helper\Helper;

class ControllerTest extends PHPUnit_Framework_TestCase
{
    public $wordpress;

    public function setUp()
    {
        $this->wordpress = wordpress();
    }

    public function testCanBeInstanciated()
    {
        $this->assertTrue(new TestController() instanceof Controller);
    }

    public function testCanAutoloadShortcodes()
    {
        $router = RouteMaker::routeToTest();
        $router->run();

        $this->assertArrayHasKey('test_shortcode', $this->wordpress->shortcodes);
        $this->assertTrue(call_user_func($this->wordpress->shortcodes['test_shortcode']));
    }

    public function testRunsAllStepsInCorrectOrder()
    {
        $router = RouteMaker::routeToTest();
        $router->run();

        $controller = $router->route->controller;
        $stack = $controller->getStack();

        $this->assertTrue($stack === array("before", "fn", "after"));
    }

    public function testCanAssignValues()
    {
        $controller = new TestController();
        $controller->init();
        $controller->view->set("test", true);

        $this->assertTrue($controller->view->get("test"));
    }

    public function testAutoloadsHelpers()
    {
        $controller = new TestController();
        $controller->init();

        $this->assertTrue($controller->view->get("TestHelper") instanceof Helper);
        $this->assertTrue($controller->view->get("customName") instanceof Helper);

        $helper = $controller->view->get("customName");
        $this->assertEquals($helper->getConfig("extraConfig"), "testconfigvalue");
    }
}
