<?php
namespace Strata\Core;

use Strata\Utility\Inflector;
use Strata\Strata;

use Exception;
use ReflectionClass;

trait StrataObjectTrait {

    /**
     * Instantiates an object of inheriting the current class or of the current class
     * that matches a particular string.
     * @param  string $name The class name of the object
     * @param  array $config Optional configuration array passed to the object constructor
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
     * Strata view helper. Mainly used to avoid hardcoding the '\\View\\Helper\\'
     * string everywhere.
     * @param  string $name The class name of the object
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

    public static function getClassNameSuffix()
    {
        return "";
    }

    public static function getNamespaceStringInStrata()
    {
        return "";
    }

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

    public function getShortName()
    {
        $rc = new ReflectionClass($this);
        return $rc->getShortName();
    }
}
