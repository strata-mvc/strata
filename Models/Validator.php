<?php

namespace MVC\Models;

class Validator {

    protected $_errorMessage = "There has been an error with this field.";
    protected $_config = array();

    public function __construct($config)
    {
        if (is_array($config)) {
            $this->_config = $config + $this->_config;
        }
    }

    public function test($value, $context)
    {
    }

    public function getMessage()
    {
        return __($this->_errorMessage);
    }

    public function setMessage($msg)
    {
        return $this->_errorMessage = $msg;
    }
}
