<?php

namespace Strata\View\Helper;

use Strata\Core\StrataObjectTrait;
use Strata\Core\StrataConfigurableTrait;

/**
 * A base class for ViewHelper objects
 */
class Helper
{
    use StrataObjectTrait;
    use StrataConfigurableTrait;

    /**
     * {@inheritdoc}
     */
    public static function getNamespaceStringInStrata()
    {
        return "View\\Helper";
    }

    /**
     * {@inheritdoc}
     */
    public static function getFactoryScopes($name)
    {
        return array(
            self::generateClassPath($name),
            self::generateClassPath($name, false)
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getClassNameSuffix()
    {
        return "Helper";
    }

    public function __construct($config = array())
    {
        $this->configure($config);
    }
}
