<?php
namespace Strata\Model\Validator;

use Strata\Core\StrataObjectTrait;

/**
 * A base class for Validator objects
 * @todo Validator needs to use the configurable trait.
 * @todo Validator needs to be refactored without underscores as scope indicators.
 */
class Validator
{
    use StrataObjectTrait;

    public static function getNamespaceStringInStrata()
    {
        return "Model\\Validator";
    }

    public static function getClassNameSuffix()
    {
        return "Validator";
    }

    public static function getFactoryScopes($name)
    {
        return array(
            self::generateClassPath($name),
            self::generateClassPath($name, false)
        );
    }

    /**
     * @var string An error message for this validation.
     */
    protected $_errorMessage = "There has been an error with this field.";

    /**
     * @var array An associative array of options used during the validation.
     */
    protected $_config = array();

    /**
     * Configures the validator
     * @param array $config The associative configuration array
     */
    public function configure($config = array())
    {
        $this->_config = $config;
    }

    /**
     * The actual test function.
     * @param  mixed                          $value   [description]
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
