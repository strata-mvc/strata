---
layout: docs
title: Configuration
permalink: /docs/configuring/
---

## General configuration

### Wordpress

Bedrock handles most of the declaration of global configuration variables. They are declared in files found in the `config` directory. They offer guidelines on the best way of using these in [their wiki](https://github.com/roots/bedrock/wiki/Configuration-files).

### Environment dependent values

Strata will have access to all the environment variables declared in the `.env` file located at the base of the project. It is the main method of setting environment based values in Bedrock. Read more on [dotenv](https://github.com/vlucas/phpdotenv).

## Strata Configuration

Strata has one configuration file that contains all the information it needs to run correctly in your project. You can find that file in `config/strata.php`.

The configuration file returns an array to Strata that can specify a custom **namespace**, automated **custom post type** creation, **routes** and **custom defined variables**.

~~~ php
<?php

$strata = array(
    "namespace" => "Superproject",

    // Setup custom routing on the app
    "routes" => array(
        //  Regular routes
        array('GET|POST', '/my-custom-page-for-volunteers/', 'VolunteersController#create'),
        array('GET|POST', ' /en/my-custom-page-for-volunteers/', 'VolunteersController#create'),

        // Admin and ajax
        array('POST', '/wp-admin/admin-ajax.php', 'AjaxController'),

        // Dynamic urls
        array('GET', '/song-listing/', 'SongController#view'),
        array('GET', '/song-listing/[*:slug]/', 'SongController#view'),
    ),

    // Automate the creation of backend based post types.
    "custom-post-types" => array(
        "Volunteer",
        "Song",
    ),

    // Set up email routing
    "project_email_list" => array(
        "info" => "info@mydomain.ca",
        "no-reply" => "no-reply@mydomain.ca",
    )
);

return $strata;
?>
~~~

The previous example mentions custom namespacing (explained next), [routing](/docs/routes/), dynamic instantiation of [custom post types](/docs/models/custom_post_types/) and user defined [configuration values](/docs/mvc#Custom values).


### Project naming

By default, your project classes will be appended to the `App` namespace. If you wish to customize this value to something that better defines your application, you may define the `namespace` key of the configuration array in `config/strata.php`.

~~~ php
<?php
    $strata["namespace"] = "ValidNamespaceName";
?>
~~~

<p class="warning">
    Should you change the namespace mid-project, after having generated project files, you will have to change the value of the namespace inside all of the classes manually.
</p>
