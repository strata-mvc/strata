<?php

use Strata\View\View;

class ViewTest extends PHPUnit_Framework_TestCase
{
    public $view;

    public function setUp()
    {
        $this->view = new View();
        $this->view->set("test", true);
    }

    public function testCanAddVariables()
    {
        $variables = $this->view->getVariables();

        $this->assertArrayHasKey('test', $variables);
        $this->assertTrue($variables['test']);
    }

    public function testRendersJson()
    {
        $testData = array(
            "testing" => true
        );

        ob_start();
        $this->view->render(array("content" => $testData, "end" => false));
        $returnValue = ob_get_clean();

        $this->assertEquals(json_encode($testData), $returnValue);
    }

    public function testRendersStringContent()
    {
        $testData = "I am testing";

        ob_start();
        $this->view->render(array("content" => $testData, "end" => false));
        $returnValue = ob_get_clean();

        $this->assertEquals($testData, $returnValue);
    }

    public function testReplacesTemplateVariables()
    {
        $parsed = $this->view->loadTemplate("test");
        $expected = "The result of this test is true"; // where 'true' is the view var we set in setUp()
        $this->assertEquals($expected, $parsed);
    }
}
