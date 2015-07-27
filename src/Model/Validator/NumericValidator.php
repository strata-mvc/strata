<?php

namespace Strata\Model\Validator;

class NumericValidator extends Validator {

    protected $_errorMessage = "Only numeric values are allowed.";

    public function test($value, $context)
    {
        return !preg_match("/\D/i", $value);
    }

}
