---
layout: docs
title: Configuration
permalink: /docs/configuring/
---

## General configuration

### Wordpress

Bedrock handles most of the declaration of global configuration variables. They are declared in files found in the `config` directory. They offer guidelines on the best way of using these in [their wiki](https://github.com/roots/bedrock/wiki/Configuration-files).

### Environment dependent values

Strata will have access to every environment variables declared in the `.env` file located at the base of the project. It is the main method of setting sensitive environment based values in Strata. Read more on [dotenv](https://github.com/vlucas/phpdotenv).

For less sensitive configuration settings, you will find a special `.php` configuration file for each environments under `config/environments/`. The file that is loaded is chosen using the value of `WP_ENV`, which needs to be set as a server variable.

By default, the loaded environment is always `development`. This is something to keep in mind when publishing your project because this level allows logging and errors to be displayed.

## Strata configuration

Strata has one configuration file that contains the information it needs to run correctly in your project. You can find that file in `config/strata.php`.

The configuration file returns an array to Strata that can specify a custom optional **namespace**, automated **custom post type** creation, **routes** and **custom defined variables**.

~~~ php
<?php

$strata = array(
    "namespace" => "Superproject",

    // Setup custom routing on the app
    "routes" => array(
        //  Exact matches
        array('GET|POST', '/my-custom-page-for-volunteers/', 'VolunteersController#create'),
        array('GET|POST', ' /en/my-custom-page-for-volunteers/', 'VolunteersController#create'),
        array('POST', '/wp-admin/admin-ajax.php', 'AjaxController'),

        // Dynamic matches
        array('GET', '/song-listing/', 'SongController#view'),
        array('GET', '/song-listing/[*:slug]/', 'SongController#view'),

        // Resource based routing (see the declared custom post types)
        array('resources' => array('Poll', 'Song')),

        // Dynamic routing
        array('GET|POST|PATCH|PUT|DELETE', "/([:controller]/([:action]/([:params]/)?)?)?"),
    ),

    // Automate the creation of backend based post types.
    "custom-post-types" => array(
        "Poll",
        "Song",
    ),

    // Declare custom project variables
    "project_email_list" => array(
        "info" => "info@mydomain.ca",
        "no-reply" => "no-reply@mydomain.ca",
    )
);

// This is a regular PHP array, you can manipulate it as you wish.
// Just know that Wordpress is not yet available at this point.
if ((int)date("j") === 1) {
    $strata["we_are_the_first"] = true;
}

return $strata;
?>
~~~

The previous example mentions custom namespacing (explained bellow), [routing](/docs/routes/), dynamic instantiation of [custom post types](/docs/models/custom_post_types/) and user defined [configuration values](/docs/mvc#Custom values).


### Project naming

By default, your project classes will be created in the `App` namespace. If you wish to customize this value to something that better defines your application, you may define the `namespace` key in the configuration array in `config/strata.php`.

~~~ php
<?php
    $strata["namespace"] = "ValidNamespaceName";
?>
~~~

<p class="warning">
    Should you change the namespace mid-project you will have to change the namespace value of the all previously generated project files.
</p>
