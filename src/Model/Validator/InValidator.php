<?php

namespace Strata\Model\Validator;

class InValidator extends Validator {

    protected $_errorMessage = "This is not a valid selection.";

    public function test($value, $context)
    {
        // We are not validating for the equivalent of required.
        if(empty($value)) {
            return true;
        }

        if (is_array($this->_config)) {
            $allowed = $this->_config;
        } elseif (is_callable($this->_config[0])) {
            $allowed = call_user_func($this->_config[0]);
        }

        return in_array($value, array_keys($allowed));
    }
}
