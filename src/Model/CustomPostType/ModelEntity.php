<?php
namespace Strata\Model\CustomPostType;

use Strata\Strata;
use Strata\Utility\Inflector;
use Exception;

class ModelEntity
{

        /**
     * Generates a possible namespace and classname combination of a
     * Strata controller. Mainly used to avoid hardcoding the '\\Model\\Entity\\'
     * string everywhere.
     * @param  string $name The class name of the model entity
     * @return string       A fully namespaced model entity name
     */
    public static function generateClassPath($name)
    {
        return Strata::getNamespace() . "\\Model\\Entity\\" . self::generateClassName($name);
    }

    public static function generateClassName($name)
    {
        $name = str_replace("-", "_", $name);
        $name = Inflector::classify($name);

        if (!preg_match("/Entity$/", $name)) {
            $name .= "Entity";
        }

        return $name;
    }

    public static function factory($name)
    {
        $classpath = self::generateClassPath($name);
        if (class_exists($classpath)) {
            return new $classpath();
        }

        throw new Exception("Strata : No file matched the model entity '$classpath'.");
    }

    private $associatedObject;

    function __construct($associatedObject = null)
    {
        if (!is_null($associatedObject)) {
            $this->bindToObject($associatedObject);
        }
    }

    function __get($var)
    {
        if (is_null($this->associatedObject)) {
            throw new Exception('ModelEntity was not linked to a Wordpress object.');
        }

        return $this->associatedObject->{$var};
    }

    function __set($var, $value)
    {
        if (is_null($this->associatedObject)) {
            throw new Exception('ModelEntity was not linked to a Wordpress object.');
        }

        return $this->associatedObject->{$var} = $value;
    }

    public function bindToObject($associatedObject)
    {
        $this->associatedObject = $associatedObject;
    }

}
