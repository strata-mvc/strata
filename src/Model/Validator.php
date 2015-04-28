<?php
namespace Strata\Model;

/**
 * A base class for Validator objects
 */
class Validator {

    /**
     * @var string An error message for this validation.
     */
    protected $_errorMessage = "There has been an error with this field.";


    /**
     * @var array An associative array of options used during the validation.
     */
    protected $_config = array();

    /**
     * Constructor
     * @param array $config The associative configuration array
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            $this->_config = $config + $this->_config;
        }
    }

    /**
     * The actual test function.
     * @param  mixed $value   [description]
     * @param  \Strata\View\Helper\FormHelper $context The form context upon which the tests are being done
     * @return boolean          True if the test passes
     */
    public function test($value, $context)
    {
        return true;
    }

    /**
     * Fetches the global error message for this validation.
     * @return [type] [description]
     */
    public function getMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * Sets the global error message for this validation.
     * @return string The message.
     */
    public function setMessage($msg)
    {
        return $this->_errorMessage = $msg;
    }
}
