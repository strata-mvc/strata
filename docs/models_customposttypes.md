---
layout: docs
title: Custom Post Types
permalink: /docs/models/customposttypes/
---

In regular MVC frameworks a model can (but is not required to) map to a table in the database. In Strata, there is no direct link between a model and a table because we do not want to use an ORM and stray outside of the Wordpress ecosystem.

Instead our models may link to a custom post type entity. We can then leverage Wordpress' tools to read the data related to this object and ensure the model is accessible across the whole environment. This method work especially well when using [Advanced Custom Fields](http://www.advancedcustomfields.com/) so you can add different object attributes than those available by default to the post object.

Semantically, the Model can be simplified as being a representation of a database table. It should contain the model definition (what is needs for Wordpress to load it) and queries or very general concepts related to the model.

On the other hand, ModelEntities can be seen as a database row. Model entities can be used to code functions based on the current values of the entity. Database queries will always return sets of Entities. Either the entity matching the current model, or the general one from which all entities inherit.

## Creating a Custom Post Type

You can generate models that will allow database operations in that they map to custom post types. To do do, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

~~~ sh
$ ./strata generate customposttype Song
~~~

It will generate a couple of files for you, including the actual model file and test suites for the generated class. The model will extend `App\Model\AppCustomPostType` and gain access to DB manipulation objects and methods.

~~~ sh
Scaffolding model Song
  ├── [ OK ] src/model/Song.php
  ├── [ OK ] src/model/Entity/SongEntity.php
  └── [ OK ] tests/model/SongTest.php
~~~

## Enabling

By default, custom post types are not automatically instantiated in Wordpress. To inform Strata it needs to load a new post type, you must add the declaration to `config/strata.php` under the `custom-post-types` key.

~~~ php
<?php
$strata = array(

    "routes" => array(
        array('GET|POST', "/([:controller]/([:action]/([:params]/)?)?)?"),
        array('POST',     '/wp/wp-admin/admin-ajax.php',    'AjaxController'),
        array('GET|POST',   '/[.*]',       'AppController#index')
    ),

    "custom-post-types" => array(
        "Song",
        "Event"
    )

);
?>
~~~

This explicit inclusion allows for distinction between wrapper models and actual dynamic post types.

You could create wrapper classes against post types that have not been created by your code. For instance, you could map BBPress topics by creating a model similar to the following example. You would gain all the functionality of a Strata Custom Post Type event if you do not declare the model yourself.

~~~ php
<?php
namespace App\Model;

class ForumPost extends AppCustomPostType {

    public function getWordpressKey()
    {
        return "reply";
    }
}
?>
~~~

# Configuring the declaration

You can further customize the post type's instantiation by passing an array along with the post type. This is how you would declare [automated admin menus]({{ site.baseurl }}/docs/models/adminmenus/) or [resource-based routes]({{ site.baseurl }}/docs/routes/).


## Customizing the CustomPostType Model

The generated entity will be only accessible through Wordpress' backend. The intend of the Model entity is not to be displayed on the front end using the default Wordpress Loop.

You can customize the model declaration by supplying the optional `$configuration` public attribute. It allows you to customize the configuration array that is internally sent to `register_post_type`. As long as you follow the [conventions](http://codex.wordpress.org/Function_Reference/register_post_type) your post type will be created using these customized values, filling the missing options with their default counterparts.

The following example illustrates how we allow the `editor` feature and also make the custom post type accessible in the frontend using the `music-page` slug (ex: `yourwebsite.com/music-page/weezer/`).

~~~ php
<?php
namespace App\Model;

class Artist extends AppCustomPostType {

    public $configuration = array(
        "supports"  => array( 'title', 'editor' ),
        'publicly_queryable' => true,
        "rewrite"   => array(
            'slug' => 'music-page'
        )
    );

}
?>
~~~

## On automated configuration

The custom post type key is generated from the model's class name. By default, this value will be prefixed by `cpt_`. In this example the unique key of the custom post type will be `cpt_artist`.

At all times, you can get the Wordpress key of the model using `wordpressKey()` statically or `getWordpressKey()` from a instance of the object.

~~~ php
<?php
$model = new App\Model\Fruit();
echo $model->getWordpressKey();

$data = new WP_Query(array(
    'post_type'     => \App\Model\Profile::wordpressKey()
));
?>
~~~


## EntityModels attributes validation

If you supply attributes in the ModelEntity, you will be able to pass this entity through our [FormHelper object]({{ site.baseurl }}/docs/helpers/formhelper/) and automatically validate posted data. Read more on how to use custom and default [validators]({{ site.baseurl }}/docs/validators/).

This is useful when you want to take user-submitted information, validate the data and save the info as custom post types with additional business logic applied to and implied from the saved information.

~~~ php
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

    // The posted value is expected to come from a Controller.
    // $posted == $this->request->data();
    public function save($posted)
    {
        $ourData = $posted[$this->getInputName()];

        // Create the post type entity
        $postId = self::create(array(
            'post_title'    => $ourData['firstname'] . " " . $ourData["lastname"],
            'post_status'   => 'publish',
        ));

        foreach (array_keys($this->getAttributes()) as $attribute) {
            // Do something with the posted value.
            // A good method is to declare fields using the same name as the attributes
            // in the Advanced Custom Fields plugin.
            // and calling something like :
            //  update_field($attribute, $ourData[$attribute], $postId);

            // In the meantime, we can print the posted value to the screen.
            debug($ourData[$attribute]);
        }

        return $postId;
    }
}
?>
~~~

## Insert, Create, Delete

Models extending `\Strata\Model\CustomPostType\Entity` will inherit static functions named `create`, `update` and `delete` that map to `wp_insert_post`, `wp_update_post` and `wp_delete_post`. They supports the same arguments as their Wordpress counterparts.

They are not intended to replace core Wordpress functions but provide a way to manipulate the data through inheritance before sending it to Wordpress.

~~~ php
<?php

$songId = Song::create(array(
    'post_title'    => "Tribute",
    'post_status'   => "pending"
));

$otherSongId = 9;
Song::update($otherSongId, array(
    'post_status'   => "publish"
));

$horribleSongId = 11;
Song::delete($horribleSongId);

?>
~~~
