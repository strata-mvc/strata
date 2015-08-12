<?php
namespace Tests\Fixtures\Model;

class TestCustomPostType extends \Strata\Model\CustomPostType\Entity {

    public $admin_menus = array(
        "exportprofiles" => array(
            "route" => "TestController",
            "title" => "Export",
            "menu-title" => "Export"
        )
    );

}
