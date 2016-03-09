<?php

namespace Strata\Model\Validator;



class BetweenValidator extends Validator
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
        // This gets confusing
        return $this->includes() ?
            $this->testExclusion($value, $context) :
            $this->testInclusion($value, $context);
    }

    private function includes()
    {
        return $this->hasConfig("includes") && (bool)$this->getConfig("includes");
    }

    private function testExclusion($value, $context)
    {
        if ($this->hasConfig("min")) {
            $min = $this->getConfig("min");
            if (round($value) < round($min)) {
                return false;
            }
        }

        if ($this->hasConfig("max")) {
            $max = $this->getConfig("max");
            if (round($value) > round($max)) {
                return false;
            }
        }

        return true;
    }

    private function testInclusion($value, $context)
    {
        if ($this->hasConfig("min")) {
            $min = $this->getConfig("min");
            if (round($value) <= round($min)) {
                return false;
            }
        }

        if ($this->hasConfig("max")) {
            $max = $this->getConfig("max");
            if (round($value) >= round($max)) {
                return false;
            }
        }

        return true;
    }
}
