---
layout: guidebook
title: Extending Default Validators
permalink: /guidebook/1.0/digging-in/validators/extending-default-validators/
covered_tags: validators, model-entities, form-helper, oop
menu_group: validators
---

If a bundled Validator does not do all you desire you may extend the, in your project. In this example, we will allow translation on the default error message of the `PostexistValidator`.

{% highlight php linenos %}
<?php
namespace App\Model\Validator;

use Strata\Model\Validator\PostexistValidator as StrataPostExist;

class PostexistValidator extends StrataPostExist {

    public function getMessage()
    {
        return __("This post could not be found.", "App");
    }

}
?>
{% endhighlight %}
