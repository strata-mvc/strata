<?php
namespace Strata\Model\CustomPostType;

use Strata\Strata;
use Strata\Utility\Inflector;
use Strata\Utility\Hash;

use Strata\Model\Validator\Validator;

use Exception;
use ReflectionClass;

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
        $name = Inflector::underscore($name);
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

    public $attributes  = array();
    private $associatedObject;
    private $validationErrors = array();

    function __construct($associatedObject = null)
    {
        if (!is_null($associatedObject)) {
            $this->bindToObject($associatedObject);
        }

        $this->normalizeAttributes();
    }

    function __get($var)
    {
        if (is_null($this->associatedObject)) {
            throw new Exception('ModelEntity was not linked to a Wordpress object.');
        }

        if (property_exists($this->associatedObject, $var)) {
            return $this->associatedObject->{$var};
        }
    }

    function __set($var, $value)
    {
        if (is_null($this->associatedObject)) {
            return $this->{$var} = $value;
        }

        return $this->associatedObject->{$var} = $value;
    }

    public function __isset($name)
    {
        return isset($this->associatedObject->{$name});
    }


    public function bindToObject($associatedObject)
    {
        $this->associatedObject = $associatedObject;
    }

    public function isBound()
    {
        return !is_null($this->associatedObject);
    }

    public function validates(array $requestData)
    {
        $ourData = Hash::extract($requestData, $this->getInputName());

        $entityErrors = array();
        foreach ($this->getAttributeNames() as $name) {
            $entityErrors[$name] = array();
            $validations = $this->extractNormalizedValidations($name);

            foreach ($validations as $validationKey => $validatorConfig) {
                $validator = Validator::factory($validationKey);
                $validator->configure($validatorConfig);

                if (!$validator->test($ourData[$name], $this)) {
                    $entityErrors[$name][$validationKey] = $validator->getMessage();
                }
            }
            if (!count($entityErrors[$name])) {
                unset($entityErrors[$name]); // this is pretty ugly.
            }
        }

        $this->validationErrors = $entityErrors;
        return count($this->validationErrors) < 1;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    public function hasValidationErrors()
    {
        return count($this->getValidationErrors()) > 0;
    }

    public function getErrors($name)
    {
        $errors = $this->getValidationErrors();

        if (array_key_exists($name, $errors)) {
            return $errors[$name];
        }

        return array();
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    protected function getAttributeNames()
    {
        return array_keys($this->getAttributes());
    }

    public function isSupportedAttribute($attr)
    {
        return in_array($attr, array_keys($this->getAttributes()));
    }

    protected function hasAttributeValidation($attr)
    {
        return Hash::check($this->getAttributes(), "$attr.validations");
    }

    public function getInputName()
    {
        return strtolower($this->getShortName());
    }

    public function getShortName()
    {
        $rc = new ReflectionClass($this);
        return $rc->getShortName();
    }

    public function getWordpressKey()
    {
        $name = $this->getShortName();
        $name = str_replace("Entity", "", $name);

        $table = Entity::factory($name);
        return $table->getWordpressKey();
    }

    private function extractNormalizedValidations($attr)
    {
        return Hash::normalize(Hash::extract($this->getAttributes(), "$attr.validations"));
    }

    private function normalizeAttributes()
    {
        $this->attributes = Hash::normalize($this->attributes);
    }

}
