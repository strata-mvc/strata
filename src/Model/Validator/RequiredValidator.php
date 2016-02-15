<?php
namespace Strata\Model\Validator;


class RequiredValidator extends Validator
{

    protected $_config = array(
        "if" => null
    );

    function __construct()
    {
        $this->setMessage(__("This is a required field.", "strata"));
    }

    public function test($value, $context)
    {
        // Should one of the conditions be missing, the validator
        // will return a successful test.
        if (!is_null($this->_config['if'])) {

            $request =  Strata::app()->getCurrentController()->request;

            foreach ($this->_config['if'] as $key => $expectedValue) {
                $comparedValue = $request->isPost() ? $request->post($key) : $request->get($key);
                if ($comparedValue !== $expectedValue) {
                    // ignore the rest of the validations, $expectedValue is not met
                    // therefore it's not a case when we need to validate the actual value.
                    return true;
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
