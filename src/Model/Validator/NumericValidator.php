<?php

namespace Strata\Model\Validator;

use Strata\Model\Validator;

class NumericValidator extends Validator {

    public $_errorMessage = "Only numeric values are allowed.";

    public function test($value, $context)
    {
        return !preg_match("/\D/i", $value);
    }

}
