<?php
namespace Strata\Model\CustomPostType;

use Strata\Strata;
use Strata\Utility\Inflector;
use Strata\Utility\Hash;

use Strata\Model\Validator\Validator;
use Strata\Core\StrataObjectTrait;

use Exception;

class ModelEntity
{
    use StrataObjectTrait;

    public static function getNamespaceStringInStrata()
    {
        return "Model\\Entity";
    }

    public static function getClassNameSuffix()
    {
        return "Entity";
    }

    public $attributes  = array();
    private $associatedObject;
    private $validationErrors = array();

    public function __construct($associatedObj = null)
    {
        if (!is_null($associatedObj)) {
            $this->bindToObject($associatedObj);
        }

        $this->normalizeAttributes();
        $this->init();
    }

    public function init()
    {
    }

    public function __get($var)
    {
        if (is_null($this->associatedObject)) {
            throw new Exception( get_class($this) . ' was not linked to a Wordpress object.');
        }

        if (property_exists($this->associatedObject, $var)) {
            return $this->associatedObject->{$var};
        }
    }

    public function __set($var, $value)
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


    public function bindToObject($obj)
    {
        $this->associatedObject = $obj;
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

                if (!array_key_exists($name, $ourData) || !$validator->test($ourData[$name], $this)) {
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

    public function getModel()
    {
        $name = $this->getShortName();
        $name = str_replace("Entity", "", $name);

        return CustomPostType::factory($name);
    }

    public function getWordpressKey()
    {
        $model = $this->getModel();
        return $model->getWordpressKey();
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
