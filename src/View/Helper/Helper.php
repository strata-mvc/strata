<?php
namespace Strata\View\Helper;

use Strata\Core\StrataObjectTrait;

/**
 * A base class for ViewHelper objects
 */
class Helper {

    use StrataObjectTrait;

    public static function getNamespaceStringInStrata()
    {
        return "View\\Helper";
    }

    public static function getFactoryScopes($name)
    {
        return array(
            self::generateClassPath($name),
            self::generateClassPath($name, false)
        );
    }

    public static function getClassNameSuffix()
    {
        return "Helper";
    }

}
