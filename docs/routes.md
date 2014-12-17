---
layout: docs
title: Configuring Routes
permalink: /docs/routes/
---

## Configuring app.php

Routes are declared inside `app.php` found at `/lib/wordpress-mvc/` under your theme's directory.

A routing rule consists of an array of 3 sections :

    * The supported request type
    * The actual permalink to match
    * The destination controller object and function.

Here are some example :

~~~ php
    array('GET|POST',   '/2014/12/hello-world/',     'HelloworldController#view')
    array('GET|POST',   '/participate/volunteer/',     'VolunteersController#create')
    array('POST',       '/wp-admin/admin-ajax.php',    'AjaxController#index')
    array('GET',        '/schools/[*:slug]/',  'SchoolsController#view')
~~~

## Complex requests

Under the hood, Wordpress MVC uses AltoRouter. As long as you enter the rounting rule in array notation, it will work. More information about complex routing can be found on [Altorouter's documention page](https://github.com/dannyvankooten/AltoRouter).

## On translated pages and dynamic urls

To be as unobstrusive as possible we chose not to support dynamic urls and page translations mapping. However, you can map multiple urls to the same controller. Therefore you can expect the following example to work the way you would think :

~~~ php
    array('GET|POST',   '/en/original-english-url/',     'HelloworldController#view')
    array('GET|POST',   '/fr/traduction-francaise/',      'HelloworldController#view')
~~~

Url rewrites that are being added when creating custom post types are never taken into account when routing towards a controller file. In other words if the MVC has generated a post type that adds its own rewrite rule (ex: School created the url_rewrite "/local-schools/"), the frameworks does not automatically map this new route to an implicit controller object. One would have to add a rule manually.
