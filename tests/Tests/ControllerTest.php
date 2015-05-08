<?php

use Strata\Controller\Controller;

use Tests\Fixtures\Router\RouteMaker;
use Tests\Fixtures\Controller\TestController;

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

}
