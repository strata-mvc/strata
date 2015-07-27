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

        $allowed = call_user_func($this->_config[0]);
        return in_array((int)$value, array_keys($allowed));
    }
}
