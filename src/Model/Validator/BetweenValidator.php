<?php

namespace Strata\Model\Validator;

class BetweenValidator extends Validator {

    protected $_errorMessage = "This is not a valid selection.";
    protected $_config = array(
        "min" => null,
        "max" => null
    );

    public function test($value, $context)
    {
        if (!is_null($this->_config['min'])) {
            if ((int)$value < (int)$this->_config['min']) {
                return false;
            }
        }

        if (!is_null($this->_config['max'])) {
            if ((int)$value > (int)$this->_config['max']) {
                return false;
            }
        }

        return true;
    }
}
