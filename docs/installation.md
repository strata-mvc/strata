---
layout: docs
title: Installation
permalink: /docs/installation/
---

## Get the library using Composer

To install Wordpress MVC create a `composer.json` file in your root directory and add the requirement to Wordpress MVC to this file.

The most recent release can be found on our [GitHub](https://github.com/francoisfaubert/wordpress-mvc/releases/latest)

~~~ json
{
    "name": "Mynamespace/Mywebsite",
    "require": {
        "francoisfaubert/wordpress-mvc": "{{ site.current_version }}"
    }
}
~~~

Afterwards, run the installation from the root directory of your wordpress project to fetch these required packages:

~~~ bash
$ php composer.phar install
~~~

This will create a directory named `vendor` at the base of your Wordpress installation where all your PHP dependencies will be located. For more information on Composer you can [read up on their documentation](https://getcomposer.org/doc/).


## Configuring in Wordpress

Wordpress MVC is theme-based in that you can load the library with different controllers and models or a different configurations for each specific themes.

All the project-related code you will write using the MVC must be located in `/lib/wordpress-mvc/` under your theme's directory. Additionally, a required file called `app.php` will located at the root of this directory and will contain the project's configuration values.

We have created a sample version of the `app.php` file in our Composer package named `app.php.default` for you to copy in your active project.

For example, should you be setting up WMVC using the `twentyfourteen` theme:

~~~ bash
$ mkdir wp-content/themes/twentyfourteen/lib/wordpress-mvc
$ cp vendor/francoisfaubert/wordpress-mvc/app.php.default wp-content/themes/twentyfourteen/lib/wordpress-mvc/app.php
~~~

In `app.php`, the only required parameter is the `key` variable. It represents the namespace of your app and will be used when autoloading your modules. For instance, if your website is called Bob's Fishing Emporium the namespace could be `Bobsfishingemporium`.

~~~ php
<?php
$app = array(
    // Give a namespace
    "key" => "Bobsfishingemporium",

    // Followed by optional additional configuration values. Ex:

    // Setup custom routing on the app
    "routes" => array(
        array('GET|POST',   '/2014/12/hello-world/',     'HelloworldController#view'),
    )
);
?>
~~~

You can [add custom configuration values]({{ site.baseurl }}/docs/configuration), [customize routes]({{ site.baseurl }}/docs/routes/) and [automatically create models model]({{ site.baseurl }}/docs/models/) to the configuration file.

## Kickstarting in Wordpress

To kickstart WMVC, open your current theme's `functions.php` file and include the bootstraper.

We encourage placing the include call in `functions.php` for consistency across projects. In reality, the actual place or method you use to include the file does not really matter.

~~~ php
<?php
// Load up MVC bootstrapper for wordpress
require_once ABSPATH . 'vendor' . DIRECTORY_SEPARATOR . 'francoisfaubert' . DIRECTORY_SEPARATOR . 'wordpress-mvc' . DIRECTORY_SEPARATOR . 'bootstrap.php';
?>
~~~
