---
layout: guidebook
title: FormHelper
permalink: /guidebook/1.0/digging-in/helpers/form-helper/
covered_tags: views, helpers, form-helper
menu_group: Helpers
---

The `FormHelper` is a class that eases the generation of forms used to create entities of a predefined model. It is not intended to be used by forms that don't save something to a custom post type entity.

## Using the form helper


The `FormHelper` can be loaded through regular means using the `Form` helper key. Should you have a local View class also named `FormHelper` that inherits Strata's then your version will be loading.

This type of inheritance can be useful should you wish to customize return values.


## API

The following is the list of available tools provided by the `Strata\View\Helper\FormHelper` class. More in-depth information can be obtained on it's [detailed API page](/api/1.0/classes/Strata_View_Helper_FormHelper.html).

## Public methods

### $FormHelper->create($modelEntity, $configuration = array());

The helper's `create` method is used to open a new `form` html tag by associating it to a `ModelEntity` and further customizing html attributes.

{% highlight php linenos %}

<div class="contact-form">

    <?php echo $FormHelper->create($productContactApplication, array(
        "action" => $productContactApplication->action . "#product-contact-application",
        "id" => "product-contact-application"
    )); ?>

</div>

{% endhighlight %}

Additional information sent through the `$configuration` hash will be parsed into html attributes except for the `type` key.

Type should be a valid HTTP method: `POST`, `GET`, `PATCH` or `file` which will instruct Strata to specify the `enctype` attribute.


### $FormHelper->end();

The helper's `end` method simply closes the form.

{% highlight php linenos %}

    ...

    <?php echo $FormHelper->end(); ?>

</div>

{% endhighlight %}



### $FormHelper->honeypot();

The helper's `honeypot` method will generate a honeypot field named after the first parameter.

{% highlight php linenos %}

    <?php echo $FormHelper->honeypot("my-fake-field-name"); ?>

{% endhighlight %}

The generated html is as follows :

{% highlight php linenos %}
<div class="validation" style="height: 1px; overflow: hidden; padding:1px 0 0 1px; position: absolute; width: 1px; z-index: -1">
    <input autocomplete="off" class="" id="data_fieldname" name="fieldname" type="text" value="">
</div>
{% endhighlight %}

It is up to you to validate the honeypot from your controllers once a request has been posted.

{% highlight php linenos %}
<?php
namespace App\Controller;

use App\Model\ContactApplication;

class ContactController extends AppController
{
    public function index()
    {
        $contact = ContactApplication::getEntity(new \stdClass());

        if ($this->request->isPost()) {
            if ($this->request->requestValidates($contact, "my-fake-field-name")) {
                debug("it does validate!");
            } else {
                debug("it doesn't validate.");
            }
        }
    }
}
?>
{% endhighlight %}

### $FormHelper->id($attributeName);

Get the internally generated `id` of any field using the `id()` method. The associated ModelEntity `$attribute` array must contain the `$attributeName` key.

{% highlight php linenos %}

    <label for="<?php echo $FormHelper->id("query"); ?>">A custom label</label>
    <?php echo $FormHelper->input("query"); ?>

{% endhighlight %}


### $FormHelper->name($attributeName);

Get the internally generated `name` of any field using the `name()` method. The associated ModelEntity `$attribute` array must contain the `$attributeName` key.

{% highlight php linenos %}

    jQuery('input[name="<?php echo <?php echo $FormHelper->name("zipcode"); ?>"]').hide();

{% endhighlight %}


### $FormHelper->submit($configuration);

Adds an html `button` of type `submit` to the form. Additional information sent through the `$configuration` hash will be parsed into except attributes expect for the `label` key which will be used as button label if present.

{% highlight php linenos %}

    <?php echo $FormHelper->submit(array(
        "label" => "Search",
        "disabled" => "disabled"
    )); ?>

{% endhighlight %}


### $FormHelper->button($inputName, $configuration);

Adds an html `button` to the form. Additional information sent through the `$configuration` hash will be parsed into html attributes except for the `label` key which will be used as button label if present.

{% highlight php linenos %}

    <?php echo $FormHelper->button(array(
        "type" => "button",
        "label" => "Search",
        "onclick" => "alert('foo!')"
    )); ?>

{% endhighlight %}

### $FormHelper->input($inputName, $configuration);

Adds an html field to the form. Additional information sent through the `$configuration` hash will be parsed into html attributes. The associated ModelEntity `$attribute` array must contain the `$attributeName` key.

The most important configuration key is the `type` as it is how the type of field is defined. If `type` is not set `FormHelper` will fallback to `text`.


{% highlight php linenos %}
 <?php echo $FormHelper->input("range", array(
    "type" => "select",
    "class" => "zoomLevel",
    "choices" => array(
        // skipping 0 means for easier validation
        1 => "5 km",
        2 => "20 km",
        3 => "50 km",
    )
)); ?>
{% endhighlight %}

{% highlight php linenos %}
<?php echo $FormHelper->input("multiple[1]", array(
    "type" => "checkbox",
    "value" => 3,
    "label" => "A 3rd choice",
)); ?>
{% endhighlight %}

{% highlight php linenos %}
<?php echo $FormHelper->input("City", array("label" => "City")); ?>
{% endhighlight %}

{% highlight php linenos %}
<?php echo $FormHelper->input("message", array("type" => "textarea", "label" => "Message *")); ?>
{% endhighlight %}


### $FormHelper->generateInlineErrors($attributeName);

Generates the possible validation errors on the `$inputName` field. Useful when you do not want Strata to automatically handle the printing of errors on your fields. The associated ModelEntity `$attribute` array must contain the `$attributeName` key.

In this case two fields using Bootstraps' grid are linked together and it would be impractical to have the error messages printed twice in each one of their smaller alignment boxes. You can turn off error rendering and decide when to print the fields error messages.

{% highlight php linenos %}
    <div class="row gender clearfix">
        <div class="col-md-6">
            <?php echo $FormHelper->input('gender', array(
                'type' => 'radio',
                'label' => 'Mrs',
                'value' => 1,
                'error' => false,
            )); ?>
        </div>
        <div class="col-md-6">
            <?php echo $FormHelper->input('gender', array(
                'type' => 'radio',
                'value' => 2,
                'label' => 'M',
                'error' => false,
            )); ?>
        </div>
    </div>

    <?php if ($modelEntity->hasValidationErrors()) : ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $FormHelper->generateInlineErrors('gender'); ?>
            </div>
        </div>
    <?php endif ?>
{% endhighlight %}
