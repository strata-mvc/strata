<?php
namespace Strata\Model\CustomPostType;

use Strata\Utility\Hash;
use Strata\Strata;
use Strata\Model\Validator\Validator;
use Strata\Model\CustomPostType\EntityTable;

class Entity extends EntityTable
{
    public $attributes  = array();

    function __construct()
    {
        $this->_normalizeAttributes();
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function isSupportedAttribute($attr)
    {
        return in_array($attr, array_keys($this->getAttributes()));
    }

    public function hasAttributeValidation($attr)
    {
        return Hash::check($this->getAttributes(), "$attr.validations");
    }

    public function attemptAttributeSet($attr, $value, $formObject = null)
    {
        $attributeErrors = array();
        if ($this->hasAttributeValidation($attr)) {

            $validations = $this->_extractNormalizedValidations($attr);
            foreach ($validations as $validationKey => $validatorConfig) {

                $validator = Validator::factory($validationKey);
                $validator->configure($validatorConfig);

                if (!$validator->test($value, $formObject)) {
                    $attributeErrors[$validationKey] = $validator->getMessage();
                }
                break;
            }
        }
        return $attributeErrors;
    }

    public function validateForm($formObject, $dataset)
    {
        $validationErrors = array();
        $validAssignments = array();

        // Check each of the values in the dataset prefixed with this
        // entities' short class name for availability and validators.
        foreach ($dataset as $key => $value) {
            $errors = null;
            if ($this->isSupportedAttribute($key)) {
                $errors = $this->attemptAttributeSet($key, $value, $formObject);
                if (count($errors)) {
                    $validationErrors[$key] = $errors;
                } else {
                    $validAssignments[$key] = $value;
                }
            }
        }

        return array(
            "errors"    => $validationErrors,
            "assigned"  => $validAssignments
        );
    }

    public function getPostPrefix()
    {
        $rc = new \ReflectionClass($this);
        return strtolower($rc->getShortName());
    }

    private function _extractNormalizedValidations($attr)
    {
        return Hash::normalize(Hash::extract($this->getAttributes(), "$attr.validations"));
    }

    private function _normalizeAttributes()
    {
        $this->attributes = Hash::normalize($this->attributes);
    }
}
