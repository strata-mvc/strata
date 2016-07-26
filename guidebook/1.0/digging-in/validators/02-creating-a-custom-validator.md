---
layout: guidebook
title: Creating a Custom Validator
permalink: /guidebook/1.0/digging-in/validators/creating-a-custom-validator/
covered_tags: validators, model-entities, form-helper, custom
menu_group: validators
---

To generate a custom validator, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a validator extending the one you passed (or the base validator if nothing is passed)

{% include terminal_start.html %}
{% highlight bash linenos %}
$ bin/mvc generate validator MyPasswordValidator
{% endhighlight %}
{% include terminal_end.html %}

You class must implement a function named `test` which will run the actual test. This function is expected to return a boolean value. The two parameters are the posted value and the FormHelper object. Using the FormHelper reference allows you to reach out to posted values and various contextual information.

To customize the error message of your validator, implement the function `getMessage`. This function is called when the test has failed and the form object is gathering error messages.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model\Validator;

use Strata\Model\Validator as StrataValidator

class MyPasswordValidator extends StrataValidator {

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
{% endhighlight %}
{% include terminal_end.html %}
