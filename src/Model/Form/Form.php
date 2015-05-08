<?php

namespace Strata\Model\Form;

use Strata\Utility\Hash;
use Strata\Controller\Request;
use Strata\View\Helper\FormHelper;
use Strata\Model\Form\ValidationCollector;

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

    /**
     * The view associated with the form.
     * @var \Strata\View\View
     */
    private $_view = null;


    private $_validationCollector = null;


    public $request = null;

    public $attributes = array();


    public function __construct(\Strata\Controller\Request $request, \Strata\View\View $view)
    {
        $this->_normalizeAttributes();
        $this->request = $request;
        $this->_assignHelper(new FormHelper($this->request));
        $this->_linktoView($view);

        $this->_validationCollector = new ValidationCollector($this);
    }


    public function getShortName()
    {
        $rc = new \ReflectionClass($this);
        return $rc->getShortName();
    }

    public function toHtml()
    {
        $tplPath = 'forms' . DIRECTORY_SEPARATOR . $this->_getStepFilename();
        return $this->_view->loadTemplate($tplPath);
    }

    /**
     * Specifies if the step was validated against the linked entity succesfully
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
        return $this->_validationCollector->getErrors();
    }

    public function hasErrors()
    {
        return $this->_validationCollector->hasErrors();
    }

    public function getAssignments()
    {
        return $this->_validationCollector->getAssignments();
    }

    public function hasAssignments()
    {
        return $this->_validationCollector->hasAssignments();
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
        $this->_validateNonce();

        $this->_collectValidationsStatus($entities);

        if ($this->hasStepsAndWantsToGoBackwards()) {
            return $this->_goBackOneStep();
        }

        if ($this->hasStepsAndWantsToGoToStep()) {
            return $this->_goToImpliedStep();
        }

        if ($this->hasStepsAndWantsToGoForward()) {
            return $this->_goForwardOneStep();
        }

        // Implying no form steps
        if ($this->isSubmitting()) {
            return $this->_doBasicSubmission();
        }

        // Return a catch all success flag has errors would have
        // exit the function by now.
        $this->_success = !$this->hasErrors();
        return $this->_success;
    }

    public function hasStepsAndWantsToGoBackwards()
    {
        $helper = $this->getHelper();
        return $helper->hasSteps() && $helper->contextWantsToGoBackwards();
    }

    public function hasStepsAndWantsToGoToStep()
    {
        $helper = $this->getHelper();
        return $helper->hasSteps() && $helper->contextWantsToGoToStep();
    }

    public function hasStepsAndWantsToGoForward()
    {
        $helper = $this->getHelper();
        return $helper->hasSteps() && $helper->contextWantsToGoForward();
    }

    public function isSubmitting()
    {
        return $this->_formHelper->contextWantsToSubmit();
    }

    // Go back if the form was posted asking to go back
    private function _goBackOneStep()
    {
        // losing everything on the current page. Though the values
        // are not kept alive and handled by the form helper, they are
        // still in the $_POST object.
        if ($this->_formHelper->currentStep > 1) {
            $this->_formHelper->currentStep--;
        }

        return $this->_setupSuccessReset();
    }

    // Go to specified step if the form was posted asking to go back
    private function _goToImpliedStep()
    {
        // losing everything on the current page. Though the values
        // are not kept alive and handled by the form helper, they are
        // still in the $_POST object.
        if ($this->_formHelper->currentStep > 1) {
            $this->_formHelper->currentStep = (int)$this->_formHelper->getPostedValue(FormHelper::POST_KEY_GO_TO_STEP);
        }

        return $this->_setupSuccessReset();
    }

    // Go forward if there are steps and there are no errors
    private function _goForwardOneStep()
    {
        // Block when there are errors
        if ($this->hasErrors()) {
            $this->_success = false;
            return $this->_success;
        }

        $this->_success = true;
        if ($this->_formHelper->currentStep < $this->_formHelper->stepsQty) {
            $this->_formHelper->currentStep++;
        } else {
            $this->_completed = true;
        }

        return $this->_completed;
    }

    private function _doBasicSubmission()
    {
        // Block when there are errors
        if ($this->hasErrors()) {
            $this->_success = false;
            return $this->_success;
        }

        $this->_completed = true;
        $this->_success = true;
        return $this->_completed;
    }

    // we didn't validate anything and because of it there weren't any errors.
    private function _setupSuccessReset()
    {
        $this->_completed = false;
        $this->_success = true;
        $this->_errors = array();

        return $this->_success;
    }

    private function _collectValidationsStatus($entities = array())
    {
        $this->_validationCollector->collect($entities);
        $this->_formHelper->applyValidationCollection($this->_validationCollector);
    }


    /**
     * This does not work as I was expected
     * @throws if invalid nonce
     */
    private function _validateNonce()
    {
        return;

        if (Hash::check($_POST, "mvc-nonce")) {
            if (!wp_verify_nonce($this->_formKey, "mvc-nonce")) {
                throw new \Exception("The form is invalid or expired.");
            }
        }
    }

    private function _normalizeAttributes()
    {
        // Set default attributes on the form
        $this->attributes += array(
            "stepsQty" => 1,
            "formKey" => "form"
        );

        $this->attributes = Hash::normalize($this->attributes);
    }

    private function _getStepFilename()
    {
        $stepKey = "";
        if ($this->isCompleted()) {
            $stepKey = ".complete";
        } else {
            if ($this->_formHelper->hasSteps()) {
                $stepKey = ".step" . $this->_formHelper->getCurrentStep();
            }
        }

        return $this->_formKey . $stepKey;
    }

    private function _linktoView(\Strata\View\View $view)
    {
        $this->_view = $view;
        $this->_view->set($this->getShortName(), $this->getHelper());
    }

    private function _assignHelper($frmHelper)
    {
        $this->_formHelper = $frmHelper;
        $this->_formHelper->stepsQty = $this->attributes['stepsQty'];

        $this->_formKey = $this->attributes['formKey'];
    }

}
