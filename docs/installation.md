---
layout: docs
title: Installation
permalink: /docs/installation/
---

## Get the library using Composer

Though Wordpress does not yet implement composer, we have hopes it will one day embrace the plugin manager. To install Wordpress MVC, create a `composer.json` file in your root directory. Add the requirement to Wordpress MVC to this file.

~~~ json
{
    "name": "Mynamespace/Mywebsite",
    "require": {
        "francoisfaubert/wordpress-mvc": "dev-master"
    }
}
~~~

Afterwards, run the installation from the root directory of your wordpress installation to fetch these required packages:

~~~ bash
$ php composer.phar install
~~~

This will create a directory named `vendors` at the base of your Wordpress installation where all your PHP dependencies will be located. For more information on Composer, you can [read up on their documentation](https://getcomposer.org/doc/).


## Configuring in Wordpress

The Wordpress MVC is theme-based in that you can load the library with different code or a different configuration when running a specific theme.

All the project-related code you will write using the MVC will be located in `/lib/wordpress-mvc/` under your theme's directory. Additionally, all the project configuration must be found in a file called app.php that is location in your projects `wordpress-mvc/` directory. We have created a starter version of the app.php file in our Composer package.

For example, should you be using the `twentyfourteen` theme:

~~~ bash
$ mkdir wp-content/themes/twentyfourteen/lib/wordpress-mvc
$ cp vendor/francoisfaubert/wordpress-mvc/app.php.default wp-content/themes/twentyfourteen/lib/wordpress-mvc/app.php
~~~

In `app.php`, the only required parameter is the `key` variable. It represent the main namespace of your app and will be used when autoloading your modules. For instance, if your website is called Bob's fishing Emporium the namespace could be `Bobsfishingemporium`.

Additionally, you can [add custom configuration values]({{ site.baseurl }}/docs/configuration), [customize routes]({{ site.baseurl }}/docs/routes/) and [automatically create models model]({{ site.baseurl }}/docs/models/).

## Kickstarting in Wordpress

To kickstart the MVC, open your current theme's `functions.php` file and include the bootstraper.

We encourage placing the include call in `functions.php` for consistency across projects, but the actualy place or method you use to include the file does not matter.

~~~ php
// Load up MVC bootstrapper for wordpress
require_once ABSPATH . 'vendor' . DIRECTORY_SEPARATOR . 'francoisfaubert' . DIRECTORY_SEPARATOR . 'wordpress-mvc' . DIRECTORY_SEPARATOR . 'bootstrap.php';
~~~
