<?php

namespace Strata\Model\Validator;

use Strata\Model\Validator;

class RequiredValidator extends Validator {

    public $_errorMessage = "This is a required field.";

    public $_config = array(
        "if" => null
    );

    public function test($value, $context)
    {
        // Should one of the conditions be missing, the validator
        // will return a successful test.
        if (!is_null($this->_config['if'])) {
            foreach ($this->_config['if'] as $key => $expectedValue) {
                $comparedValue = $context->getPostedValue($key);
                if (is_array($comparedValue) && count($comparedValue) === 1) {
                    $comparedValue = $comparedValue[0];
                }

                if ($comparedValue !== $expectedValue) {
                    return true; // ignore the validation, $expectedValue is not met
                }
            }
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            return !empty($trimmed);
        }

        // Array are expected to be lists on which we are
        // validating integer values, not string values.
        if (is_array($value)) {
            foreach ($value as $key => $currentValue) {
                if ((int)$currentValue > 0) {
                    return true;
                }
            }
            return false;
        }
    }
}
