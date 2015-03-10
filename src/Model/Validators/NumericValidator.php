<?php

namespace MVC\Model\Validators;

use MVC\Model\Validator;

class NumericValidator extends Validator {

    public $_errorMessage = "Only numeric values are allowed.";

    public function test($value, $context)
    {
        return !preg_match("/\D/i", $value);
    }

}
