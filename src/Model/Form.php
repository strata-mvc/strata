<?php

namespace MVC\Model;

use MVC\Utility\Hash;
use MVC\Controller\Request;
use MVC\View\Helper\FormHelper;

class Form {

    protected $_formHelper = null;
    protected $_formKey = null;

    /**
     * Refers to a successfully validated step / process
     */
    protected $_success = false;

    /**
     * Refers to a completed, validated form
     */
    protected $_completed = false;
    protected $_request = null;


    public function __construct()
    {
        $this->_request = new Request();
        $this->init();
    }

    // Calling init right away after the construction is awkward.
    // The configuration should be inherited class attributes
    public function init($options = array())
    {
        $options += array(
            "stepsQty" => 1,
            "formKey" => "form"
        );

        $this->_formHelper = new FormHelper();
        $this->_formHelper->stepsQty = $options['stepsQty'];
        $this->_formKey = $options['formKey'];
    }

    /**
     * Shortcodes loose the global scope so we have to assign view vars again.
     * @param string The name of the template to load (.php will be added to it)
     * @param step Should be form have multiple steps, the step to load
     * @param array an associative array of values to assign in the template
     */
    public static function loadTemplateFile($name, $step = null, $values = array())
    {
        $stepKey = "";
        if (is_string($step)) {
            $stepKey = ".$step";
        } elseif((int)$step > 0) {
            $stepKey = ".step$step";
        }

        ob_start();
        // expose local variables for the email template
        extract($values);
        include(get_template_directory() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . $name . $stepKey .'.php');
        return  ob_get_clean();
    }

    /**
     * Form are loaded through shotcodes and are therefore outside the regular
     * wordpress request that triggered the rendering of the page. We need to send
     * instanciated controller variables when loading the form template file.
     */
    public function toHtml($values = array())
    {
        // Assign the helper
        $rc = new \ReflectionClass($this);
        $values[$rc->getShortName()] = $this->_formHelper;

        // Load the file
        $step = null;
        if ($this->isCompleted()) {
            $step = "complete";
        } else {
            if ($this->_formHelper->hasSteps()) {
                $step = $this->_formHelper->getCurrentStep();
            }
        }

        return self::loadTemplateFile($this->_formKey, $step, $values);
    }

    /**
     * Specifies if the form was validated against the linked entity succesfully
    */
    public function isValid()
    {
        return $this->_success;
    }

    public function isCompleted()
    {
        return $this->isValid() && $this->_completed;
    }

    public function getErrors()
    {
        if ($this->hasErrors()) {
            return $this->getHelper()->errors;
        }
    }

    public function hasErrors()
    {
        return count($this->getHelper()->errors) > 0;
    }

    public function getAssignments()
    {
        if (count($this->getHelper()->assigned)) {
            return $this->getHelper()->assigned;
        }
    }

    public function getHelper()
    {
        return $this->_formHelper;
    }


    /**
     * Process the current step and if the form validates, goes to the next step (if applicable)
     */
    public function process(array $entities)
    {
        /** this does not work as I was expected
        if (Hash::check($_POST, "mvc-nonce")) {
            if (!wp_verify_nonce($this->_formKey, "mvc-nonce")) {
                throw new \Exception("The form is invalid or expired.");
            }
        }
        */

        // Validate posted values against the entities if there is one set
        foreach ($entities as $entity) {
            // should we not use $_POST directly?
            $entityValues = $this->_request->post(FormHelper::POST_WRAP . "." . $entity->getPostPrefix());

            // Switch between multiple entities posted, or just one.
            if(!array_key_exists(0, $entityValues)) {
                $this->_checkEntityValues($entity, $entityValues);
            } else {
                foreach ($entityValues as $idx => $repeatingEntityValues) {
                    $this->_checkEntityValues($entity, $repeatingEntityValues, $idx);
                }
            }
        }

        // Go back if the form was posted asking to go back
        if ($this->_formHelper->hasSteps() && $this->_formHelper->contextWantsToGoBackwards()) {
            // losing everything on the current page. Though the values
            // are not kept alive and handled by the form helper, they are
            // still in the $_POST object.
            if ($this->_formHelper->currentStep > 1) {
                $this->_formHelper->currentStep--;
            }

            // we didn't validate anything and because of it there weren't any errors.
            $this->_success = true;
            $this->errors = array();
            return $this->_success;
        }


        // Go to specified step if the form was posted asking to go back
        if ($this->_formHelper->hasSteps() && $this->_formHelper->contextWantsToGoToStep()) {
            // losing everything on the current page. Though the values
            // are not kept alive and handled by the form helper, they are
            // still in the $_POST object.
            if ($this->_formHelper->currentStep > 1) {
                $this->_formHelper->currentStep = (int)$this->_formHelper->getPostedValue(FormHelper::POST_KEY_GO_TO_STEP);
            }

            // we didn't validate anything and because of it there weren't any errors.
            $this->_success = true;
            $this->errors = array();
            return $this->_success;
        }


        // Go forward if there are steps and there are no errors
        if ($this->_formHelper->hasSteps() && $this->_formHelper->contextWantsToGoForward()) {
            // Block when there are errors
            if ($this->hasErrors()) {
                $this->success = false;
                return $this->success;
            }

            if ($this->_formHelper->currentStep < $this->_formHelper->stepsQty) {
                $this->_formHelper->currentStep++;
            } else {
                $this->_completed = true;
            }
        }

        if ($this->_formHelper->contextWantsToSubmit()) {
            // Block when there are errors
            if ($this->hasErrors()) {
                $this->success = false;
                return $this->success;
            }

            $this->_completed = true;
        }

        // Return a catch all success flag has errors would have
        // exit the function by now.
        $this->_success = !$this->hasErrors();
        return $this->_success;
    }

    protected function _checkEntityValues($entity, array $entityValues, $idx = null)
    {
        $feedback = $entity->validateRaw($this->getHelper(), $entityValues);
        $fieldName = $entity->getPostPrefix();
        if (!is_null($idx)) {
            $fieldName .= "[".$idx."]";
        }

        foreach ($feedback["errors"] as $attr => $error) {
            $this->getHelper()->errors[sprintf("%s[%s]", $fieldName, $attr)] = $error;
        }
        foreach ($feedback["assigned"] as $attr => $assignedValue) {
            $this->getHelper()->assigned[sprintf("%s[%s]", $fieldName, $attr)] = $assignedValue;
        }
    }

}
