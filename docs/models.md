---
layout: docs
title: Models
permalink: /docs/models/
---

## Model entities, tables and Custom Post Types

Models are where the logic being the website's custom processes, validations and everything that is generally defined as "business logic" is located.

In regular MVC frameworks a model can (but is not required to) map to a table in the database. In Wordpress MVC, there is no direct link between a model and a table because we do not want to use an ORM and stray this far outside of the Wordpress ecosystem.

Instead, our models link to a custom post type entity. We can then leverage Wordpress' tools to read the data related to this object and ensure the model is accessible across the whole environment. This method work especially well when using [Advanced Custom Fields](http://www.advancedcustomfields.com/) so you can add different object attributes than those available to the post object.

Because of the adoption of Wordpress' methods every model requests will return __arrays__ and not model entities. Something to keep in mind when manupulating the received data.

## Writing a model declaration

Here's how you could declare the model of a School entity:

~~~ php
<?php
namespace Mywebsite\Models;

use MVC\CustomPostTypes\Entity;

class School extends Entity
{
    public static $key         = 'school';
    public static $singular    = 'School';
    public static $plural      = 'Schools';

    // Optional
    public static $options = array(
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
    );

    // You would need to define the entity's attributes if you wish to
    // automate form validation, or expose them to other classes. That is the cost of
    // not having an ORM.
    public $attributes = array(
        "name"      => array("validations" => array("required")),
    )
}
?>
~~~

First, you must ensure the class extends `MVC\CustomPostTypes\Entity` so it inherits model methods.

The `key` attribute defines the name of the custom post type internally. By default, this value will be prefixed by `ctp_` so that in this example the unique key of the custom post type will be `ctp_school`. The `singular` and `plural` attributes allow for the quicker generation of labels.

Finally, the optional `options` attribute allows you to customize the configuration array that is sent to `register_post_type` internally. As long as you follow the [conventions](http://codex.wordpress.org/Function_Reference/register_post_type) your post type will be created using these customized values, filling the missing options with their default counterparts.

## Entity attributes validation

If you supply entity attributes, you will be able to use this entity along side our [FormHelper]({{ site.baseurl }}/docs/helpers/formhelper/) and automatically validate data. Read more on how to use custom and default [validators]({{ site.baseurl }}/docs/validators/).

## Inserting

Models extending `MVC\CustomPostTypes\Entity` will inherit a static function named `create` that maps to `wp_insert_post` and supports the same arguments.

~~~ php
<?php
use Mywebsite\Models\School;

$schoolId = School::create(array(
    'post_title'    => "School of Rock",
    'post_status'   => School::STATUS_PENDING
));
?>
~~~

## Updating

This is not yet implemented.

## Deleting

This is not yet implemented.
