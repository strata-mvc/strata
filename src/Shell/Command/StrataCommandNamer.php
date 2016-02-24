<?php

namespace Strata\Shell\Command;

use Strata\Core\StrataObjectTrait;

/**
 * Base class for Shell Command reflection.
 * This class contains a basic tool set to perform repetitive visual outputs.
 * It is also the interface between Strata and Symfony's codebase.
 */
class StrataCommandNamer
{
    use StrataObjectTrait;

    /**
     * {@inheritdoc}
     */
    public static function getNamespaceStringInStrata()
    {
        return "Shell\\Command";
    }

    /**
     * {@inheritdoc}
     */
    public static function getClassNameSuffix()
    {
        return "Command";
    }
}
