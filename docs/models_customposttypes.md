---
layout: docs
title: Custom Post Types
permalink: /docs/models/customposttypes/
---

In regular MVC frameworks a model can (but is not required to) map to a table in the database. In Strata, there is no direct link between a model and a table because we do not want to use an ORM and stray this far outside of the Wordpress ecosystem.

Instead our models may link to a custom post type entity. We can then leverage Wordpress' tools to read the data related to this object and ensure the model is accessible across the whole environment. This method work especially well when using [Advanced Custom Fields](http://www.advancedcustomfields.com/) so you can add different object attributes than those available to the post object.

Because of the adoption of Wordpress' methods, every model requests will return __arrays of posts__. Something to keep in mind when manipulating the received data.

## Creating a Custom Post Type

You can generate models that will allow database operations in that they map to custom post types. To do do, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

~~~ sh
$ bin/strata generate customposttype Song
~~~

It will generate a couple of files for you, including the actual model file and test suites for the generated class. The model will extend `Strata\Model\CustomPostType\Entity` and gain access to DB manipulation objects and methods.

~~~ sh
Scaffolding model Song
  ├── [ OK ] src/model/Song.php
  └── [ OK ] tests/model/SongTest.php
~~~

## Customizing the CustomPostType Model

The generated entity will be only accessible through Wordpress' backend. The intend of the Model entity is not to be displayed on the front end using the default Wordpress Loop.

You can customize the model declaration by supplying the optional `$configuration` public attribute. It allows you to customize the configuration array that is sent to `register_post_type` internally. As long as you follow the [conventions](http://codex.wordpress.org/Function_Reference/register_post_type) your post type will be created using these customized values, filling the missing options with their default counterparts.

The following example illustrates how we allow the `editor` and also allow the custom post types to be accessible using the `music-page` slug (ex: `yourwebsite/music-page/weezer/`).

~~~ php
<?php
namespace MyProject\Model;

class Artist extends \Strata\Model\CustomPostType\Entity {

    public $configuration = array(
        "supports"  => array( 'title', 'editor' ),
        'publicly_queryable' => true,
        "rewrite"   => array(
            'slug'                => 'music-page',
            'with_front'          => true,
        )
    );

}
?>
~~~

## On automated configuration

The custom post type key is generated from the model's class name. By default, this value will be prefixed by `ctp_`. In this example the unique key of the custom post type will be `ctp_artist`.

At all times, you can get the Wordpress key of the model using `wordpressKey()`.

~~~ php
<?php
$data = new \WP_Query(array(
    'post_type'     => Profile::wordpressKey()
));
?>
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
