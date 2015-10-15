<?php
namespace Strata\Shell\Command;

use Strata\Core\StrataObjectTrait;

/**
 * Base class for Shell Command reflection.
 * This class contains a basic toolset to perform repetitive visual outputs.
 * It is also the interface between Strata and Symfony's codebase.
 */
class StrataCommandNamer
{
    use StrataObjectTrait;

    public static function getNamespaceStringInStrata()
    {
        return "Shell\\Command";
    }

    public static function getClassNameSuffix()
    {
        return "Command";
    }

}
