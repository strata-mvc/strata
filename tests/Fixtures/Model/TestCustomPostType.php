<?php
namespace Tests\Fixtures\Model;

use Strata\Model\CustomPostType\CustomPostType;

class TestCustomPostType extends CustomPostType
{

    public $admin_menus = array(
        "exportprofiles" => array(
            "route" => "TestController",
            "title" => "Export",
            "menu-title" => "Export"
        )
    );
}
