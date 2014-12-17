---
layout: docs
title: Models
permalink: /docs/models/
---

## Model entities, tables and Custom Post Types

Models are where the logic being the website's custom processes, validations and everything that is generally defined as "business logic" is located.

In regular MVC frameworks a model can, but is not required to, map to a table in the database. In Wordpress MVC, there is no direct link between a model and a table because we do not want to use an ORM or stay outside of Wordpress. Instead our models link to a custom post type entity. We can then leverage Wordpress' tools to read data related to this object and ensure the model is accessible across the whole environment.

Because of the adoption of Wordpress' methods, every model requests will return arrays and not model entities. Something to keep in mind when manupulating the received data.

## Writing a model declaration

Here's how you could declare the model of a School entity:

~~~ php
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
    // automated form validation
    public $attributes = array(
        "name"      => array("validations" => array("required")),
    )
}
~~~

First, you must ensure the class extends `MVC\CustomPostTypes\Entity` which does all the heavy lifting for you.

The `key` attribute defines the name of the custom post type internally. By default, this value will be prefixed by `ctp_` so that in the exemple the unique key of this custom post type will be `ctp_school`. The `singular` and `plural` attributes allow for the quick generation of labels.

Finally, the optional `options` attribute allows you to customize the configuration array that is sent to `register_post_type`. As long as you follow the [conventions](http://codex.wordpress.org/Function_Reference/register_post_type) your post type will be created using these customized values, filling the missing options with their default counterparts.

## Entity attributes validation

If you supply entity attributes, you will be able to use this entity along side our [FormHelper]({{ site.baseurl }}/docs/helpers/formhelper/) and automatically validate data. Read more on how to use custom and default [validators]({{ site.baseurl }}/docs/validators/).

## Inserting

Models extending `MVC\CustomPostTypes\Entity` will inherit a static function named `create` that maps to `wp_insert_post` and supports the same arguments.

~~~ php
use Mywebsite\Models\School;

$schoolId = School::create(array(
    'post_title'    => "School of Rock",
    'post_status'   => School::STATUS_PENDING
));
~~~

## Updating

This is not yet implemented.

## Deleting

This is not yet implemented.
