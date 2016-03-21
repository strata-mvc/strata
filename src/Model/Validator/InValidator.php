<?php

namespace Strata\Model\Validator;

use Exception;

class InValidator extends Validator
{

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMessage(__("This is not a valid selection.", $this->getTextdomain()));
    }

    /**
     * {@inheritdoc}
     */
    public function test($value, $context)
    {
        // We are not validating for the equivalent of required.
        if (empty($value)) {
            return true;
        }

        $allowed = $this->getAllowedValues();

        return in_array((int)$value, array_keys($allowed));
    }

    private function getAllowedValues()
    {
        $configuration = $this->configuration;

        if (is_array($configuration)) {
            if (count($configuration) === 1 && is_callable($configuration[0])) {
                return call_user_func($configuration[0]);
            }
            return $configuration;
        }

        throw new Exception("InValidator received an incorrect type of allowed object.");
    }
}
