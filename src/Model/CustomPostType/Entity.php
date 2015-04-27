<?php
namespace Strata\Model\CustomPostType;

use Strata\Utility\Hash;
use Strata\Strata;
use Strata\Model\CustomPostType\EntityTable;

class Entity extends EntityTable
{
    // Entity props
    public $attributes  = array();

    public function getAttributes()
    {
        return Hash::normalize($this->attributes);
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
        $appNamespace = \Strata\Strata::getNamespace();
        $validations = Hash::extract($this->getAttributes(), "$attr.validations");

        if (count($validations)) {
            foreach (Hash::normalize($validations) as $validationKey => $validatorConfig) {
                // Check for custom validators in the Strata scope as well as in
                // the project scope.
                $scopes = array(
                    $appNamespace . "\\Model\\Validator\\" . ucfirst($validationKey) . "Validator",
                    "Strata\\Model\\Validator\\" . ucfirst($validationKey) . "Validator",
                );

                foreach ($scopes as $validatorName) {
                    if (class_exists($validatorName)) {
                        $validator = new $validatorName($validatorConfig);
                        if (!$validator->test($value, $formObject)) {
                            $attributeErrors[$validationKey] = $validator->getMessage();
                        }
                        break;
                    }
                }
            }
        }

        return $attributeErrors;
    }

    public function validateRaw($formObject, $dataset)
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
}
