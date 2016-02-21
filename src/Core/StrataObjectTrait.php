<?php

namespace Strata\Core;

use Strata\Utility\Inflector;
use Strata\Strata;
use ReflectionClass;
use Exception;

/**
 * Strata objects should all be using this Trait. It ensures
 * classes can be declared and instantiated the same way.
 * It also validates the naming conventions within Strata.
 */
trait StrataObjectTrait
{
    /**
     * Instantiates an object that uses the StrataObjectTrait which class name
     * matches the $name value.
     * @param  string $name   The class name of the object
     * @param  array  $config Optional configuration array passed to the object constructor
     * @return mixed An object of the current class
     * @throws Exception
     */
    public static function factory($name, $config = array())
    {
        $classpaths = self::getFactoryScopes($name);

        foreach ($classpaths as $classpath) {
            if (class_exists($classpath)) {
                return new $classpath($config);
            }
        }

        throw new Exception(sprintf("Strata : No object matched '%s'.", implode(", ", $classpaths)));
    }

    /**
     * Returns scopes in which Strata will look in
     * to load objects.
     * @param  string $name A class name
     * @return array
     */
    public static function getFactoryScopes($name)
    {
        return array(self::generateClassPath($name));
    }

    /**
     * Instantiates an object of the current class.
     * @return mixed An object of the current class
     */
    public static function staticFactory()
    {
        $class = get_called_class();
        return new $class();
    }

    /**
     * Generates a possible namespace and classname combination of a
     * Strata objecy. Mainly used to avoid hardcoding the '\\View\\Helper\\'
     * string everywhere (or whatever else would the namespace have been).
     * @param  string  $name  The class name of the object
     * @param  boolean $local Generated a path that is relative to the current project. Default to false.
     * @return string       A fully namespaced name
     */
    public static function generateClassPath($name, $local = true)
    {
        $paths = array(
            $local ? Strata::getNamespace() : 'Strata',
            self::getNamespaceStringInStrata(),
            self::generateClassName($name)
        );

        return implode("\\", $paths);
    }

    /**
     * Returns the default class name suffix for this object.
     * @return string
     */
    public static function getClassNameSuffix()
    {
        return "";
    }

    /**
     * Returns the default namespace path.
     * @return string
     */
    public static function getNamespaceStringInStrata()
    {
        return "";
    }

    /**
     * Generates a valid class name from the $name value.
     * @param  string $name A possible class name.
     * @return string a Valid class name.
     */
    public static function generateClassName($name)
    {
        $name = str_replace("-", "_", $name);

        if (strstr($name, "\\")) {
            $composedName = "";
            foreach (explode("\\", $name) as $namespace) {
                $namespace = Inflector::underscore($namespace);
                $namespace = Inflector::classify($namespace);
                $composedName .= $namespace . "\\";
            }
        } else {
            $name = Inflector::underscore($name);
            $name = Inflector::classify($name);
        }

        $suffix = self::getClassNameSuffix();
        if (!preg_match("/$suffix$/", $name)) {
            $name .= $suffix;
        }

        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $name)) {
            throw new Exception(sprintf("We could not generate a valid PHP classname from %s", $name));
        }

        return $name;
    }

    /**
     * Returns this object's class name without the full namespace.
     * @return string
     */
    public function getShortName()
    {
        $rc = new ReflectionClass($this);
        return $rc->getShortName();
    }
}
