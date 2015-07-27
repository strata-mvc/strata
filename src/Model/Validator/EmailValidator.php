<?php

namespace Strata\Model\Validator;

class EmailValidator extends Validator {

    protected $_errorMessage = "This does not look like a valid email.";

    public function test($value, $context)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

}
