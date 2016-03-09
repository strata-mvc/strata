<?php

namespace Strata\Model\Validator;

class EmailValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMessage(__("This does not look like a valid email.", $this->getTextdomain()));
    }

    /**
     * {@inheritdoc}
     */
    public function test($value, $context)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
