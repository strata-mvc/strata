---
layout: docs
title: Routes
permalink: /docs/routes/
---

## Configuring

Routes are declared inside `app.php` found in `/lib/wordpress-mvc/` under your theme's directory.

A routing rule is represented by an array consisting of 3 parameters :

* The supported __request type__
* The actual __permalink__ to match
* The destination __controller__ object and function.

Here are some examples :

~~~ php
<?php
$app = array(
    "key" => "Mynamespace",
    "routes" => array(
        array('GET|POST',   '/2014/12/hello-world/',        'HelloworldController#view')
        array('GET|POST',   '/participate/volunteer/',      'VolunteersController#create')
        array('POST',       '/wp-admin/admin-ajax.php',     'AjaxController#index')
        array('GET',        '/schools/[*:slug]/',           'SchoolsController#view')
    )
);
?>
~~~

In the previous example, you can see you can pipe multiple request types (useful when you have a form on the page). You can also automatically catch url parameters to variables with the use of `[*:varname]`.

## More complex requests

Under the hood Wordpress MVC uses AltoRouter. As long as you enter the rounting rule using their array notation, it will work. More information about how you can customise the rules can be found on [Altorouter's documention page](https://github.com/dannyvankooten/AltoRouter).

## On translated pages and dynamic urls

To be as unobstrusive as possible we chose not to support dynamic urls and page translations mapping. However, you can map multiple urls to the same controller. Therefore you can expect the following example to work the way you would think :

~~~ php
<?php
$app = array(
    "key" => "Mynamespace",
    "routes" => array(
        array('GET|POST',   '/en/original-english-url/',     'HelloworldController#view')
        array('GET|POST',   '/fr/traduction-francaise/',      'HelloworldController#view')
    )
);
?>
~~~

Url rewrites that are being added when creating custom post types are never taken into account when routing towards a controller file. In other words if the MVC has generated a post type that adds its own rewrite rule (ex: School created the url_rewrite "/local-schools/"), the frameworks does not automatically map this new route to an implicit controller object. One would have to add such a rule manually.
