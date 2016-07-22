<?php

namespace Strata\Middleware;

/**
 * The base Middleware class.
 */
abstract class Middleware
{
    use StrataObjectTrait;

    /**
     * {@inheritdoc}
     */
    public static function getNamespaceStringInStrata()
    {
        return "Middleware";
    }

    /**
     * {@inheritdoc}
     */
    public static function getClassNameSuffix()
    {
        return "Initializer";
    }


    /**
     * A list of shell commands being enabled by the middleware.
     * @var array
     */
    public $shellCommands = array();

    /**
     * The entry point of the middleware. The class is expected to
     * generate their own filters afterwards.
     */
    abstract function initialize();
}
