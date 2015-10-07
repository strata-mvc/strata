<?php
namespace Strata\Model\Validator;

class NumericValidator extends Validator
{

    function __construct()
    {
        $this->setMessage(__("Only numeric values are allowed.", "strata"));
    }

    public function test($value, $context)
    {
        return !preg_match("/\D/i", $value);
    }
}
