<?php
namespace Test\Fixture\Core;

use Strata\Core\StrataObjectTrait;

class TestStrataObjectTrait
{

    use StrataObjectTrait;

    public static function getClassNameSuffix()
    {
        return "Testing";
    }
}
