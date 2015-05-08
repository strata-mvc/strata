<?php

use Tests\Fixtures\Strata\Strata;

use Strata\Model\Model;
use Strata\View\View;
use Strata\Model\CustomPostType\Registrar\Registrar;

class ModelTest extends PHPUnit_Framework_TestCase
{
    public $wordpress;
    public $model;
    public $customPostType;

    public function setUp()
    {
        $this->wordpress = wordpress();
        $this->wordpress->reset();

        $this->model = Model::factory("TestStateless");
        $this->customPostType = Model::factory("TestCustomPostType");
    }

    public function testCanBeInstanciated()
    {
        $this->assertTrue($this->model instanceof Model);
        $this->assertTrue($this->customPostType instanceof Model);
    }

    /**
     * @expectedException        Exception
     */
    public function testInvalidModel()
    {
        Model::factory("I_dont_exist");
    }


    public function testCtpRegistered()
    {
        $this->wordpress->reset();

        $strata = new Strata();
        $strata->configure(array(
            "namespace" => "Tests\Fixtures",
            "custom-post-types" => array(
                "TestCustomPostType"
            )
        ));
        $strata->run();

        $this->assertArrayHasKey('init', $this->wordpress->actions);
        $this->assertEquals('registerPostType', $this->wordpress->actions['init'][0][1]);
    }

    public function testCtpAdminMenuRegistered()
    {
        $this->wordpress->reset();

        $strata = new Strata();
        $strata->configure(array(
            "namespace" => "Tests\Fixtures",
            "custom-post-types" => array(
                "TestCustomPostType" => array("admin" => array("exportprofiles" => array("route" => "TestController", "title" => "Export", "menu-title" => "Export"))),
            )
        ));
        $strata->run();

        $this->assertArrayHasKey('admin_menu', $this->wordpress->actions);
        $this->assertTrue($this->wordpress->actions['admin_menu'][0][0] instanceof Registrar);
    }
}
