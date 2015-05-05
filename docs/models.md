---
layout: docs
title: Models
permalink: /docs/models/
---

## Model entities, tables and Custom Post Types

Models are where the logic being the website's custom processes, validations and everything that is generally defined as "business logic" is located.

## Creating a model file

To generate a flat Model in which you can add business logic but cannot save entities in Wordpress' database, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

Look at [automated custom post type models](/docs/models/customposttypes/) for information on how to create models that map to database entries.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a model named `Artist` :

~~~ sh
$ bin/strata generate model Artist
~~~

It will generate a couple of files for you, including the actual model file and test suites for the generated class.

~~~ sh
Scaffolding model Artist
  ├── [ OK ] src/model/Artist.php
  └── [ OK ] tests/model/ArtistTest.php
~~~

## Entity attributes validation

If you supply entity attributes, you will be able to use this entity against our [FormHelper]({{ site.baseurl }}/docs/helpers/formhelper/) and automatically validate posted data. Read more on how to use custom and default [validators]({{ site.baseurl }}/docs/validators/).

This is useful when you want to take user-submitted information, validate the data and save the info as custom post types with additional business logic applied to and implied from the saved information.

Here is a lengthy, but complete, example of how attributes and the FormHelper can be implemented in a Model:

~~~ php
<?php
namespace Mywebsite\Model;

class CustomerDetail extends \Strata\Model\CustomPostType\Entity {
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
        "agreements"              => array("validations" => array("required", "in" => array("Mywebsite\Model\CustomerDetail::agreementsListing"))),
    );

    public static function agreementsListing()
    {
        return array(
            1 => "I agree with the terms of use and operating modalities."),
        );
    }

    public function saveForm($formHelper)
    {
        // Create the post type entity
        $postId = self::create(array(
            'post_title'    => $formHelper->getPostedValue('customer.firstname') . " " . $formHelper->getPostedValue('customer.lastname'),
            'post_status'   => 'publish',
        ));

        foreach (array_keys($this->getAttributes()) as $attribute) {
            // Do something with the posted value.
            // A good method is to declare fields using the same name as the attributes
            // in the Advanced Custom Fields plugin.
            // and calling something like :
            //  update_field($attribute, $formHelper->getPostedValue(CustomerDetail::key() . "." . $attribute), $guardianId);

            // In the meantime, we can print the posted value to the screen.
            debug($formHelper->getPostedValue(CustomerDetail::key() . "." . $attribute));
        }

        return $postId;
    }
}
?>
~~~

## Insert, Create, Delete

Models extending `\Strata\Model\CustomPostType\Entity` will inherit a static functions named `create`, `update` and `delete` that map to `wp_insert_post`, `wp_update_post` and `wp_delete_post`. They supports the same arguments as their Wordpress counterparts.

They exist only to do last minute manipulation of the data and are not intended to replace core Wordpress functions.

~~~ php
<?php

$songId = Song::create(array(
    'post_title'    => "Song of Rock",
    'post_status'   => "pending"
));

$otherSongId = 9;
Song::update($otherSongId, array(
    'post_status'   => "publish"
));

$horribleSongId = 9;
Song::delete($horribleSongId);

?>
~~~
