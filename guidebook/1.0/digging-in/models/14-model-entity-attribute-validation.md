---
layout: guidebook
title: ModelEntity Attribute Validation
permalink: /guidebook/1.0/digging-in/models/model-entity-attribute-validation/
covered_tags: models, form-helper, validation, saving
menu_group: models
---

If you supply `$attributes` in the ModelEntity, you will be able to pass this entity through our [FormHelper object](/guidebook/1.0/digging-in/helpers/form-helper/) and automatically validate posted data. Read more on how to use custom and default [Validators](/guidebook/1.0/digging-in/validators/).

This is useful when you want to take user-submitted information, validate the data and save the info as custom post types with additional business logic applied to and implied from the saved information.

{% highlight php linenos %}
<?php
namespace App\Model\Entity;

class CustomerDetailEntity extends AppCustomPostType {
{
    public $attributes = array(
        "telephone_area"            => array("validations" => array("required", "numeric", "length" => array("min" => 3, "max" => 3))),
        "telephone_centraloffice"   => array("validations" => array("required", "numeric", "length" => array("min" => 3, "max" => 3))),
        "telephone_station"         => array("validations" => array("required", "numeric", "length" => array("min" => 4, "max" => 4))),
        "telephone_ext"             => array("validations" => array("numeric")),
        "firstname"     => array("validations" => array("required")),
        "lastname"      => array("validations" => array("required")),
        "email"         => array("validations" => array("required", "email", "same" => array("as" => "email_confirm"))),
        "address"       => array("validations" => array("required")),
        "street"        => array("validations" => array("required")),
        "apt",
        "postalcode"    => array("validations" => array("required", "postalcode")),
        "city"          => array("validations" => array("required")),
        "comment",
        "agreements"              => array("validations" => array("required", "in" => array("App\Model\CustomerDetail::agreementsListing"))),
    );

    public static function agreementsListing()
    {
        return array(
            1 => "I agree with the terms of use and operating modalities."),
        );
    }
}
?>
{% endhighlight %}
