<?php

namespace Strata\Model\Validator;

use Exception;

class RegexValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMessage(__("This value doe not have the expected format.", "strata"));
    }

    /**
     * {@inheritdoc}
     */
    public function test($value, $context)
    {
        if ($this->hasConfig("pattern")) {
            return preg_match($this->getConfig("pattern"), $value);
        }

        throw new Exception("RegexValidator is missing the pattern parameter.");
    }
}
