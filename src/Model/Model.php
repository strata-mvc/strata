<?php
namespace Strata\Model;

use Strata\Utility\Inflector;
use Strata\Strata;

use Exception;
use ReflectionClass;

/**
 * A base class for model objects
 */
class Model {



    /**
     * Generates a possible namespace and classname combination of a
     * Strata controller. Mainly used to avoid hardcoding the '\\Controller\\'
     * string everywhere.
     * @param  string $name The class name of the controller
     * @return string       A fulle namespaced controller name
     */
    public static function generateClassPath($name)
    {
        return Strata::getNamespace() . "\\Model\\" . self::generateClassName($name);
    }

    public static function generateClassName($name)
    {
        $name = str_replace("-", "_", $name);
        $name = Inflector::underscore($name);
        return Inflector::classify($name);
    }

    public static function factory($name)
    {
        $classpath = self::generateClassPath($name);
        if (class_exists($classpath)) {
            return new $classpath();
        }

        throw new Exception("Strata : No file matched the model '$classpath'.");
    }

    public static function staticFactory()
    {
        $class = get_called_class();
        return new $class();
    }

    function __construct()
    {

    }

    public function getShortName()
    {
        $rc = new ReflectionClass($this);
        return $rc->getShortName();
    }

}
