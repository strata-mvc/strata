<?php

namespace Strata\Model\Validator;

class LengthValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function test($value, $context)
    {
        $length = $this->figureOutLength($value);

        if ($this->hasConfig("min")) {
            $min = $this->getConfig("min");
            if ($length < round($min)) {
                return false;
            }
        }

        if ($this->hasConfig("max")) {
            $max = $this->getConfig("max");
            if ($length > round($max)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        if (!$this->hasConfig("min") && $this->hasConfig("max")) {
            return sprintf(
                __("The length must be at most %s characters long.", $this->getTextdomain()),
                $this->getConfig("max")
            );
        } elseif ($this->hasConfig("min") && !$this->hasConfig("max")) {
            return sprintf(
                __("The length must be at least %s characters long.", $this->getTextdomain()),
                $this->getConfig('min')
            );
        } elseif (!is_null($this->getConfig('min')) && $this->getConfig('min') === $this->getConfig('max')) {
            return sprintf(
                __("The length must be exactly %s characters.", $this->getTextdomain()),
                $this->getConfig('min')
            );
        }

        return sprintf(__("The length must be between %s and %s.", $this->getTextdomain()),
            $this->getConfig('min'),
            $this->getConfig('max')
        );
    }

    public function figureOutLength($value)
    {
        $length = 0;

        if (is_array($value)) {
            foreach ($value as $val) {
                if ($val !== "0") {
                    $length++;
                }
            }
        } else {
            $length = strlen($value);
        }

        return $length;
    }
}
