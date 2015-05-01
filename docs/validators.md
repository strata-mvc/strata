---
layout: docs
title: Validators
permalink: /docs/validators/
---

Should you require to save the post data of your custom models, you may automate the validation of the entity's known attributes.

Validators should be passed as configuration arrays to the `validations` key of the model's attributes declaration:

~~~ php
<?php
namespace Mywebsite\Model;

class Song extends \Strata\Model\CustomPostType\Entity {
{
    public $attributes = array(
        "artist"            => array("validations" => array("required", "postExists")),
        'genre'             => array("validations" => array("in" => array("Mywebsite\Model\Song::genreListing"))),
        "lyrics"            => array("validations" => array("required")),
        "year_active"       => array("validations" => array("required", "numeric", "length" => array("min" => 2, "max" => 4))),
        "email"             => array("validations" => array("required", "email", "same" => array("as" => "email_confirm"))),
        'copyright',
    );

    public static function genreListing()
    {
        return array(
            1 => "Rock",
            2 => "Hip Hop"
        );
    }
}
?>
~~~

Strata comes with 8 common validators that you can use or derive from. They will automatically be used to validate the current value of the POST variable associated with this field as created by the FormHelper.

When loading validators, Strata looks in your project's namespace before trying to load the default ones. This allows you to customize existing or create new validators.

### EmailValidator

Validates an email address using PHP's `filter_var`. It has no parameters.

~~~ php
<?php
public $attributes = array(
    "attributename"      => array("validations" => array("email")),
);
?>
~~~

### InValidator

Validates that value is inside a possible list of values. The parameter is expected to be a callable function that returns an array in which the array key is the value we are looking for.

This is to help creating form controls making it easier to look in value/labels sets. It is good practice to skip the 0 index as to not prevent false positives in other Validators like the RequiredValidator.

~~~ php
<?php
public $attributes = array(
    "attributename"      => array("validations" => array("in" => array("Mywebsite\Model\Song::genreListing"))),
);

public static function genreListing()
{
    return array(
        1 => "Rock",
        2 => "Hip Hop"
    );
}
?>
~~~

### LengthValidator

Validates the length of a string or an array. Possible configurations include the `min` and `max` attributes. They are both optional and can be used together. The value entered should be an expected possibility.

In the following example, the first attribute will fail if the value is shorter than 3, passing at an exact length of 3. The second attribute will fail if the length is longer than 9, passing at a length of 9. The final attribute will fail is the value is shorter than 3 or longer than 4, passing if the value of a length of 3 or 4.

~~~ php
<?php
public $attributes = array(
    "attributename"      => array("validations" => array("length" => array("min" => 3))),
    "attributename2"     => array("validations" => array("length" => array("max" => 9))),
    "attributename3"     => array("validations" => array("length" => array("min" => 3, "max" => 4))),
);
?>
~~~

### NumericValidator

Validates the assigned value is a numeric value using a `\D` regular expression. It has no parameters.

~~~ php
<?php
public $attributes = array(
    "year_active"       => array("validations" => array("numeric")),
);
?>
~~~

### PostExistsValidator

Validates a post id exists. Useful when doing soft relationships between custom post types.

~~~ php
<?php
public $attributes = array(
    "artist"            => array("validations" => array("postExists")),
);
?>
~~~

### PostalcodeValidator

Currently only validates Canadian postal codes.

~~~ php
<?php
public $attributes = array(
    "postalcode"          => array("validations" => array("postalcode")),
);
?>
~~~

### RequiredValidator

Validates the presence of a required value.

If optionally accepts the `if` attribute allowing optionally requirements based on contextual information. It expect an array in which the key is the name of the posted object as created by the FormHelper and the value is the expected value. In the cases when the conditions are not met, the attribute will not be required.

~~~ php
<?php
public $attributes = array(
    "name"          => array("validations" => array("required")), // always required
    "specialattr"   => array("validations" => array("required" => array("if" => array("posted_field_name" => "value")))), // depends on $_POST['data[posted_field_name]']
);
?>
~~~

### SameValidator

Validates the similarity of two different variables.

If accepts a `as` attribute which represents a $_POST value key. In the example bellow, the email field is validated against a confirmation email input box.

~~~ php
<?php
public $attributes = array(
    "email"         => array("validations" => array("same" => array("as" => "email_confirm"))), // depends on $_POST['data[posted_field_name]']
);
?>
~~~

## Creating a custom validator

To generate a custom validator, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a validator extending the one you passed (or the base validator if nothing is passed)

~~~ sh
$ bin/mvc generate validator MyPasswordValidator RequiredValidator
~~~

You class must implement a function named `test` which will run the actual test. This function is expected to return a boolean value. The two parameters are the posted value and the FormHelper object. Using the FormHelper reference allows you to reach out to posted values and various contextual information.

To customize the error message of your validator, implement the function `getMessage`. This function is called when the test has failed and the form object is gathering error messages.

~~~ php
<?php
namespace Mywebsite\Model\Validator;

class MyValidator extends \Strata\Model\Validator {

    public function test($value, $context)
    {
        return $value == "what i'm expecting";
    }

    public function getMessage()
    {
        return "This is not the value we are expecting.";
    }
}
?>
~~~

## Modifying an existing validator

If a validator does not do all you desire you may extend the default ones in your project. In this example, we will allow translation on the default error message of the `PostExistsValidator`.

~~~ php
<?php
namespace Mywebsite\Model\Validator;

class PostExistsValidator extends \Strata\Model\Validator\PostExistsValidator {

    public function getMessage()
    {
        return __("This post could not be found.", "Mywebsite");
    }

}
?>
~~~

