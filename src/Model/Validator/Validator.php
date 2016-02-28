<?php

namespace Strata\Model\Validator;

use Strata\Core\StrataObjectTrait;
use Strata\Core\StrataConfigurableTrait;

/**
 * A base class for Validator objects.
 */
class Validator
{
    use StrataObjectTrait;
    use StrataConfigurableTrait;

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
    protected $errorMessage = "There has been an error with this field.";

    /**
     * Initiates the validator object. Useful for inheritance and
     * translating error labels.
     */
    public function init()
    {

    }

    /**
     * The actual test function.
     * @param  mixed                          $value
     * @param  \Strata\Model\CustomPostType\ModelEntity $context The entity upon which the tests are being done
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
        return $this->errorMessage;
    }

    /**
     * Sets the global error message for this validation.
     * @return string The message.
     */
    public function setMessage($msg)
    {
        return $this->errorMessage = $msg;
    }
}
