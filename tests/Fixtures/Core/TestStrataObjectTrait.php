<?php
namespace Tests\Fixtures\Core;

use Strata\Core\StrataObjectTrait;

class TestStrataObjectTrait  {

    use StrataObjectTrait;

    public static function getClassNameSuffix()
    {
        return "Testing";
    }


}


