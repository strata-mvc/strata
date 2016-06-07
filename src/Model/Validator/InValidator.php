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

    public function configure($values)
    {
        // Prevent parent configure() to make sure
        // the indexes don't normalize
        if (is_array($values)) {
            $this->configuration = $values;
        }
    }

    private function getAllowedValues()
    {
        $configuration = $this->configuration;

        if (is_array($configuration)) {
            if (count($configuration) === 1 && is_callable(reset($configuration))) {
                return call_user_func(reset($configuration));
            }
            return $configuration;
        }

        throw new Exception("InValidator received an incorrect type of allowed object.");
    }
}
