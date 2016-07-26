---
layout: guidebook
title: FormHelper
permalink: /guidebook/1.0/digging-in/helpers/form-helper/
covered_tags: views, helpers, form-helper
menu_group: Helpers
---

The `Strata\View\Helper\FormHelper` is a class that eases the generation of forms based on `ModelEntity` attributes. It will automate field validation and take part in much of the request handling process for you. It is not intended to be used by forms that don't save something against a custom post type entity, but you could still use it.

## Using the form helper

The `FormHelper` can be loaded through regular means using the `Form` helper key. Should you have a local View class also named `FormHelper` that inherits from Strata's then your version will be loaded.

This type of inheritance can be useful should you wish to customize return values.

## API

The following is the list of available tools provided by the `FormHelper` class. More in-depth information can be obtained on it's [detailed API page](/api/1.0/classes/Strata_View_Helper_FormHelper.html).

## Public methods

### $FormHelper->create($modelEntity, $configuration = array());

The helper's `create($entity, $config)` method is used to open a new `form` html tag by associating it to a `ModelEntity` and further customizing HTML attributes. `$entity` can be `null` in forms that are not associated to a `AppModelEntity` object.

{% include terminal_start.html %}
{% highlight php linenos %}

<div class="contact-form">

    <?php echo $FormHelper->create($productContactApplication, array(
        "action" => $productContactApplication->action . "#product-contact-application",
        "id" => "product-contact-application"
    )); ?>

    ...

{% endhighlight %}
{% include terminal_end.html %}

Additional information sent through the `$configuration` hash will be parsed into html attributes except for the `type` key.

Type should be a valid HTTP method: `POST`, `GET`, `PATCH` or `file` which will instruct Strata to specify the `enctype` attribute.


{% include terminal_start.html %}
{% highlight php linenos %}

<div class="contact-form">

    <?php echo $FormHelper->create($myEntity, array("type" => "file")); ?>

    ...

</div>

{% endhighlight %}
{% include terminal_end.html %}


### $FormHelper->end();

The helper's `end()` method simply closes the form.

{% include terminal_start.html %}
{% highlight php linenos %}

    ...

    <?php echo $FormHelper->end(); ?>

</div>

{% endhighlight %}
{% include terminal_end.html %}



### $FormHelper->honeypot();

The helper's `honeypot($key)` method will generate a honeypot field named after the `$key` parameter.

{% include terminal_start.html %}
{% highlight php linenos %}

    <?php echo $FormHelper->honeypot("honeypot-fieldname"); ?>

{% endhighlight %}
{% include terminal_end.html %}

The generated html is as follows :

{% include terminal_start.html %}
{% highlight php linenos %}
<div class="validation" style="height: 1px; overflow: hidden; padding:1px 0 0 1px; position: absolute; width: 1px; z-index: -1">
    <input autocomplete="off" class="" id="data_honeypotfieldname" name="honeypot-fieldname" type="text" value="">
</div>
{% endhighlight %}
{% include terminal_end.html %}

It is up to you to validate the honeypot from your controllers once a request has been posted.

{% include terminal_start.html %}
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
            if ($this->request->requestValidates($contact, "honeypot-fieldname")) {
                debug("it does validate!");
            } else {
                debug("it doesn't validate.");
            }
        }
    }
}
?>
{% endhighlight %}
{% include terminal_end.html %}

### $FormHelper->id($attributeName);

Get the internally generated `id` of any field using the `id()` method. The associated ModelEntity `$attributes` array must contain the `$attributeName` key.

{% include terminal_start.html %}
{% highlight php linenos %}

    <label for="<?php echo $FormHelper->id("query"); ?>">A custom label</label>
    <?php echo $FormHelper->input("query"); ?>

{% endhighlight %}
{% include terminal_end.html %}


### $FormHelper->name($attributeName);

Get the internally generated `name` of any field using the `name()` method. The associated ModelEntity `$attributes` array must contain the `$attributeName` key.

{% include terminal_start.html %}
{% highlight php linenos %}

    jQuery('input[name="<?php echo <?php echo $FormHelper->name("zipcode"); ?>"]').hide();

{% endhighlight %}
{% include terminal_end.html %}


### $FormHelper->submit($configuration);

Adds an html `button` of type `submit` to the form. Additional information sent through the `$configuration` hash will be parsed into except attributes expect for the `label` key which will be used as button label if present.

{% include terminal_start.html %}
{% highlight php linenos %}

    <?php echo $FormHelper->submit(array(
        "label" => "Search",
        "disabled" => "disabled"
    )); ?>

{% endhighlight %}
{% include terminal_end.html %}


### $FormHelper->button($inputName, $configuration);

Adds an html `button` to the form. Additional information sent through the `$configuration` hash will be parsed into html attributes except for the `label` key which will be used as button label if present.

{% include terminal_start.html %}
{% highlight php linenos %}

    <?php echo $FormHelper->button(array(
        "type" => "button",
        "label" => "Search",
        "onclick" => "alert('foo!')"
    )); ?>

{% endhighlight %}
{% include terminal_end.html %}

### $FormHelper->input($inputName, $configuration);

Adds an html field to the form. Additional information sent through the `$configuration` hash will be parsed into html attributes. The associated ModelEntity `$attributes` array must contain the `$attributeName` key.

The most important configuration key is the `type` as it is how the type of field is defined. If `type` is not set `FormHelper` will default to `text`.


{% include terminal_start.html %}
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
{% include terminal_end.html %}

{% include terminal_start.html %}
{% highlight php linenos %}
<?php echo $FormHelper->input("multiple[1]", array(
    "type" => "checkbox",
    "value" => 3,
    "label" => "A 3rd choice",
)); ?>
{% endhighlight %}
{% include terminal_end.html %}

{% include terminal_start.html %}
{% highlight php linenos %}
<?php echo $FormHelper->input("City", array("label" => "City")); ?>
{% endhighlight %}
{% include terminal_end.html %}

{% include terminal_start.html %}
{% highlight php linenos %}
<?php echo $FormHelper->input("message", array("type" => "textarea", "label" => "Message *")); ?>
{% endhighlight %}
{% include terminal_end.html %}


### $FormHelper->generateInlineErrors($attributeName);

Generates the possible validation errors on the `$inputName` field. Useful when you do not want Strata to automatically handle the printing of errors on your fields. The associated ModelEntity `$attributes` array must contain the `$attributeName` key.

In this case two fields using Bootstraps' grid are linked together and it would be impractical to have the error messages printed twice in each one of their smaller alignment boxes. You can turn off error rendering and decide when to print the fields error messages.

{% include terminal_start.html %}
{% highlight php linenos %}

    <?php echo $FormHelper->create($modelEntity); ?>

        <div class="row gender clearfix">
            <div class="col-md-6">
                <?php echo $FormHelper->input('title', array(
                    'type' => 'radio',
                    'label' => 'Mrs',
                    'value' => 1,
                    'error' => false,
                )); ?>
            </div>
            <div class="col-md-6">
                <?php echo $FormHelper->input('title', array(
                    'type' => 'radio',
                    'value' => 2,
                    'label' => 'M',
                    'error' => false,
                )); ?>
            </div>
        </div>


        <?php if ($modelEntity->hasErrors('title')) : ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo $FormHelper->generateInlineErrors('title'); ?>
                </div>
            </div>
        <?php endif ?>

    <?php echo $FormHelper->end(); ?>

{% endhighlight %}
{% include terminal_end.html %}
