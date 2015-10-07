<?php

namespace Strata\Model\Validator;

class EmailValidator extends Validator
{

    function __construct()
    {
        $this->setMessage(__("This does not look like a valid email.", "strata"));
    }

    public function test($value, $context)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
