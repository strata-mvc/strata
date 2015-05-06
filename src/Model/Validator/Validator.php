<?php
namespace Strata\Model\Validator;

use Strata\Strata;
use Exception;

/**
 * A base class for Validator objects
 */
class Validator {

    /**
     * Generate an instanciate Validator object from a possible string name.
     * @param  string $name The class name of the validator
     * @return Strata\Model\Validator\Validator       A validator
     */
    public static function factory($name)
    {
        // Check for custom validators in the Strata scope as well as in
        // the project scope. Project scope has priority.
        $scopes = array(
            self::generateClassPath($name),
            self::generateClassPath($name, false)
        );

        foreach ($scopes as $validatorName) {
            if (class_exists($validatorName)) {
                return new $validatorName();
            }
        }

        throw new Exception("Strata : No file matched the validator '$name'.");
    }

    /**
     * Generates a possible namespace and classname combination of a
     * Strata validator. Mainly used to avoid hardcoding the '\\Validator\\'
     * string everywhere.
     * @param  string $name The class name of the validator
     * @param  boolean $local Generated a path that is relative to the current project. Default to false.
     * @return string       A fulle namespaced controller name
     */
    public static function generateClassPath($name, $local = true)
    {
        $namespace = $local ? Strata::getNamespace() : 'Strata';
        return $namespace . "\\Model\\Validator\\" . ucfirst($name) . "Validator";
    }

    /**
     * @var string An error message for this validation.
     */
    private $_errorMessage = "There has been an error with this field.";

    /**
     * @var array An associative array of options used during the validation.
     */
    private $_config = array();

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
