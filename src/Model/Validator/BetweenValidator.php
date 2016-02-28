<?php
namespace Strata\Model\Validator;

class BetweenValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMessage(__("This is not a valid selection.", "strata"));
    }

    /**
     * {@inheritdoc}
     */
    public function test($value, $context)
    {
        return $this->excludes() ?
            $this->testExclusion() :
            $this->testInclusion();
    }

    private function excludes()
    {
        return $this->hasConfig("excludes") && (bool)$this->getConfig("excludes");
    }

    private function testExclusion()
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

    private function testInclusion()
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
