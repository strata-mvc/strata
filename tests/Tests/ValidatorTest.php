<?php

use Strata\Model\Model;
use Strata\Model\Form\Form;
use Strata\Model\Form\ModelValidation;

use Strata\Controller\Request;
use Strata\View\View;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    public $wordpress;
    public $model;
    public $customPostType;

    public function setUp()
    {
        $this->model = Model::factory("TestStateless");
    }

    public function testCanBeInstanciated()
    {
        $this->assertTrue($this->model instanceof Model);
    }

    public function testTwoRequiredAttributesFail()
    {
        $_POST = array(
            "firstname" => null,
            "lastname" => null,
            "optional" => "dude"
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(2, $errors);
        $this->assertArrayHasKey('firstname', $errors);
        $this->assertArrayHasKey('lastname', $errors);
        $this->assertArrayHasKey('required', $errors['firstname']);
        $this->assertArrayHasKey('required', $errors['lastname']);

        $this->assertFalse(array_key_exists('optional', $errors));
    }

    public function testInAttributeFail()
    {
        $_POST = array(
            "mixedtest" => 9999
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('mixedtest', $errors);
        $this->assertArrayHasKey('in', $errors['mixedtest']);
    }

    public function testInAttributePass()
    {
        $_POST = array(
            "mixedtest" => 2
        );

        $validation = $this->runFakeValidation($_POST);
        $assignments = $validation->getAssignments();

        $this->assertCount(1, $assignments);
        $this->assertArrayHasKey('mixedtest', $assignments);
    }

    public function testLengthAttributePass()
    {
        $_POST = array(
            "lengthtest" => "1245"
        );

        $validation = $this->runFakeValidation($_POST);
        $assignments = $validation->getAssignments();

        $this->assertCount(1, $assignments);
        $this->assertArrayHasKey('lengthtest', $assignments);
    }

    public function testLengthAttributeArrayPass()
    {
        $_POST = array(
            "lengthtest" => range(0, 4)
        );

        $validation = $this->runFakeValidation($_POST);
        $assignments = $validation->getAssignments();

        $this->assertCount(1, $assignments);
        $this->assertArrayHasKey('lengthtest', $assignments);
    }

    public function testLengthAttributeShortStringFail()
    {
        $_POST = array(
            "lengthtest" => "12"
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('lengthtest', $errors);
        $this->assertArrayHasKey('length', $errors['lengthtest']);
    }

    public function testLengthAttributeLongStringFail()
    {
        $_POST = array(
            "lengthtest" => "1234567890"
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('lengthtest', $errors);
        $this->assertArrayHasKey('length', $errors['lengthtest']);
    }

    public function testLengthAttributeLongArrayFail()
    {
        $_POST = array(
            "lengthtest" => range(0, 10)
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('lengthtest', $errors);
        $this->assertArrayHasKey('length', $errors['lengthtest']);
    }

    public function testLengthAttributeShortArrayFail()
    {
        $_POST = array(
            "lengthtest" => range(0, 1)
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('lengthtest', $errors);
        $this->assertArrayHasKey('length', $errors['lengthtest']);
    }

    public function testNumericAttributePass()
    {
        $_POST = array(
            "numerictest" => 10
        );

        $validation = $this->runFakeValidation($_POST);
        $assignments = $validation->getAssignments();

        $this->assertCount(1, $assignments);
        $this->assertArrayHasKey('numerictest', $assignments);
    }

    public function testNumericAttributeFail()
    {
        $_POST = array(
            "numerictest" => "10e"
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('numerictest', $errors);
        $this->assertArrayHasKey('numeric', $errors['numerictest']);
    }

    public function testPostcodeAttributePass()
    {
        $_POST = array(
            "postalcodetest" => "h0h0h0"
        );

        $validation = $this->runFakeValidation($_POST);
        $assignments = $validation->getAssignments();

        $this->assertCount(1, $assignments);
        $this->assertArrayHasKey('postalcodetest', $assignments);
    }

    public function testPostcodeWithSpacesAttributePass()
    {
        $_POST = array(
            "postalcodetest" => "h0h 0h0"
        );

        $validation = $this->runFakeValidation($_POST);
        $assignments = $validation->getAssignments();

        $this->assertCount(1, $assignments);
        $this->assertArrayHasKey('postalcodetest', $assignments);
    }

    public function testPostcodeAttributeFail()
    {
        $_POST = array(
            "postalcodetest" => "0h0 h0h"
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('postalcodetest', $errors);
        $this->assertArrayHasKey('postalcode', $errors['postalcodetest']);
    }

    public function testPostexistsAttributePass()
    {
        $_POST = array(
            "postexiststest" => 1
        );

        $validation = $this->runFakeValidation($_POST);
        $assignments = $validation->getAssignments();

        $this->assertCount(1, $assignments);
        $this->assertArrayHasKey('postexiststest', $assignments);
    }

    public function testSameAttributeFail()
    {
        $_POST = array(
            "sametest" => "test",
            "comparetest" => "different"
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('sametest', $errors);
        $this->assertArrayHasKey('same', $errors['sametest']);
    }

    public function testEmailAttributePass()
    {
        $_POST = array(
            "emailtest" => "francois.faubert@domain.co.uk"
        );

        $validation = $this->runFakeValidation($_POST);
        $assignments = $validation->getAssignments();

        $this->assertCount(1, $assignments);
        $this->assertArrayHasKey('emailtest', $assignments);
    }

    public function testEmailAttributeFail()
    {
        $_POST = array(
            "emailtest" => "francois.faubert@domain"
        );

        $validation = $this->runFakeValidation($_POST);
        $errors = $validation->getErrors();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('emailtest', $errors);
        $this->assertArrayHasKey('email', $errors['emailtest']);
    }

    private function runFakeValidation($dataset = array())
    {
        $form = new Form(new Request(), new View());
        $validation = $this->model->validateForm($form, $dataset);
        $this->assertTrue($validation instanceof ModelValidation);

        return $validation;
    }
}
