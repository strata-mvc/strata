<?php
namespace Tests\Fixtures\Controller;

class TestController extends \Strata\Controller\Controller
{

    public $stackorder;

    public $shortcodes = array(
        "test_shortcode" => "returnTrue"
    );

    public $helpers = array(
        "Test",
        "SecondTest" => array(
            "name" => "customName",
            "extraConfig" => "testconfigvalue"
        )
    );

    public function init()
    {
        parent::init();
        $this->stackorder = array();
    }

    public function after()
    {
        $this->stackorder[] = "after";
    }

    public function before()
    {
        $this->stackorder[] = "before";
    }

    public function returnTrue()
    {
        $this->stackorder[] = "fn";
        return true;
    }

    public function getStack()
    {
        return $this->stackorder;
    }
}
