<?php

namespace MVC\Models\Validators;

use MVC\Models\Validator;

class LengthValidator extends Validator {

    public $_config = array(
        "min" => null,
        "max" => null
    );

    public function test($value, $context)
    {
        $length = is_array($value) ? count($value) : strlen($value);

        if (!is_null($this->_config['min'])) {
            if ($length < $this->_config['min']) {
                return false;
            }
        }

        if (!is_null($this->_config['max'])) {
            if ($length > $this->_config['max']) {
                return false;
            }
        }

        return true;
    }

    public function getMessage()
    {
        if (is_null($this->_config['min']) && !is_null($this->_config['max'])) {
            return sprintf(__("The length must be at most %s characters long."), $this->_config['max']);
        } elseif (!is_null($this->_config['min']) && is_null($this->_config['max'])) {
            return sprintf(__("The length must be at least %s characters long."), $this->_config['min']);
        } elseif ($this->_config['min'] === $this->_config['max'] && !is_null($this->_config['min'])) {
            return sprintf(__("The length must be exactly %s characters."), $this->_config['min']);
        }

        return sprintf(__("The length must be between %s and %s."), $this->_config['min'], $this->_config['max']);
    }

}
