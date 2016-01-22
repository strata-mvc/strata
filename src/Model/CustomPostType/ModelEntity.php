<?php
namespace Strata\Model\CustomPostType;

use Strata\Strata;
use Strata\Utility\Inflector;
use Strata\Utility\Hash;

use Strata\Model\Validator\Validator;
use Strata\Core\StrataObjectTrait;
use Strata\Controller\Request;

use stdClass;
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
        // We bind rather than assigning the properties to this entity because
        // we don't want to inherit WP_Post.
        $this->bindToObject(!is_null($associatedObj) ? $associatedObj : new stdClass());

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

    /**
     * Assigns the entity data that may be found in the request
     * to this entity.
     * @param  Request $request
     * @return bool True when entity data was found.
     */
    public function assignRequest(Request $request)
    {
        $extracted = $this->extractData($request);

        if (count($extracted)) {
            foreach ($this->getAttributeNames() as $key) {
                if (array_key_exists($key, $extracted)) {
                    $this->{$key} = $extracted[$key];
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Extracts the request values related to the entity
     * from a supplied request object.
     * @param  Request $request
     * @return array
     */
    public function extractData(Request $request)
    {
        $data = $request->data();
        $entityData = array();
        $inputName = $this->getInputName();

        if (!array_key_exists($inputName, $data)) {
            return array();
        }

        $entityData = $data[$inputName];
        $filteredData = array();

        foreach ($this->getAttributeNames() as $key) {
            if (array_key_exists($key, $entityData)) {
                $filteredData[$key] = $entityData[$key];
            }
        }

        return $filteredData;
    }

    /**
     * Runs validation on the current entity values as declared
     * by the entity's attributes.
     * @return boolean True if validation passed
     */
    public function validates()
    {
        $currentAttributeValues = array();

        if ($this->isBound()) {
            foreach ($this->getAttributeNames() as $name) {
                $currentAttributeValues[$name] = $this->{$name};
            }
        }

        return $this->validates(array(
            $this->getInputName() => $currentAttributeValues
        ));
    }

    /**
     * Runs validation in a hash object that may or may
     * not be the result of request->data(), but has the same
     * format.
     * @return boolean True if validation passed
     */
    public function validate(array $requestData)
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
        $simpleAttr = preg_replace("/\[\d+\]$/", "", $attr);
        return in_array($simpleAttr, $this->getAttributeNames());
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
