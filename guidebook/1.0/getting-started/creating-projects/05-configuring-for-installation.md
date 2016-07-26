---
layout: guidebook
title: Configuring your application
permalink: /guidebook/1.0/getting-started/creating-projects/configuring-for-installation/

covered_tags: configuration, installation

menu_group: creating-projects
next_page: /guidebook/1.0/getting-started/creating-projects/localization/
next_page_label: Localization
---

Wordpress requires access to a database in order to run. Instead of configuring the credentials in `wp-config.php` as you may have been used to, Strata configures its projects in more flexible way.


## Configuring Sensitive Information

Database credentials, API keys and various salts should not be stored in the project. Especially not in source control. The most accepted way of handling this issue is to use server variables.

When developing locally, you may use a `.env` file stored at the root of your project. Values listed in this file will be automatically available in your project by using PHP `getenv()` function. You can read more on [dotenv](https://github.com/vlucas/phpdotenv) if you need more information on the mechanics.

{% include terminal_start.html %}
{% highlight bash linenos %}
# Database

DB_HOST=:/Applications/MAMP/tmp/mysql/mysql.sock
DB_NAME=myproject
DB_USER=root
DB_PASSWORD=root

# URLs

WP_HOME=http://127.0.0.1:5454
WP_SITEURL=${WP_HOME}/wp

# Current Environment

WP_ENV=development
{% endhighlight %}
{% include terminal_end.html %}

## Configuring by environment

On the other hand you may need to define global variables based on the specified project environment. Strata ships with support for 3 environments modes: development, staging and production.

Each of these environment have their corresponding file in `~/config/environments/` that is automatically loaded at runtime.

By default Strata loads in `development` mode.

## Configuring Strata

Strata has one configuration file that contains the information it needs to run correctly in your project. You will find that file in `config/strata.php`.

The configuration file returns an array to Strata that can specify a custom optional **namespace**, automated **custom post type** creation, **routes** and **custom defined variables**.

{% include terminal_start.html %}
{% highlight php linenos %}
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

        // Dynamic routing
        array('GET|POST|PATCH|PUT|DELETE', "/([:controller]/([:action]/([:params]/)?)?)?"),
    ),

    // Automate the creation of backend based post types.
    "custom-post-types" => array(
        "Poll",
        "Song",
    ),

    // Declare custom project variables
    "project-email-list" => array(
        "info" => "info@mydomain.com",
        "no-reply" => "no-reply@mydomain.com",
    )
);

// This is a regular PHP array, you can manipulate it as you wish.
// Note that Wordpress is not yet available at this point but Strata
// objects are.
if ((int)date("j") === 1) {
    $strata["we_are_the_first"] = true;
}

return $strata;
?>
{% endhighlight %}
{% include terminal_end.html %}

The previous example mentions custom namespaces (explained bellow), [routing](/guidebook/1.0/digging-in/routing/), dynamic instantiation of [custom post types](/guidebook/1.0/digging-in/models/custom-post-types/) and user defined configuration values.


### Project naming

By default, your project classes will be created in the `App` namespace. If you wish to customize this value to something that better defines your application, you may define the `namespace` key in the configuration array in `config/strata.php`.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
    $strata["namespace"] = "ValidNamespaceName";
?>
{% endhighlight %}
{% include terminal_end.html %}

<p class="warning">
    Should you change the namespace mid-project you will have to change the namespace value of the all previously generated project files manually.
</p>
