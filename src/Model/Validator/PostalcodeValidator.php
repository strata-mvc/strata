<?php

namespace Strata\Model\Validator;

class PostalcodeValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMessage(__("Only Canadian postal codes are accepted (ex: H0H 0H0).", $this->getTextdomain()));
        $this->setConfig("pattern", "/\w\d\w\s?\d\w\d/i");
    }
}
