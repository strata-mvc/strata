<?php
namespace Strata\Model\Validator;

class PostalcodeValidator extends Validator {

    function __construct()
    {
        $this->setMessage(__("Only Canadian postal codes are supported (ex: H0H 0H0).", "strata"));
    }

    public function test($value, $context)
    {
        return preg_match("/\w\d\w\s?\d\w\d/i", trim($value));
    }

}
