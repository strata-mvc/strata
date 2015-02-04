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
        array('GET',        array('/songs/[*:slug]?/' => 'index.php?pagename=songs'),           'SongsController#view')
    )
);
?>
~~~

In the previous example, you can see you can pipe multiple request types (useful when you have a form on the page). You can also automatically catch url parameters to variables with the use of `[*:varname]`.

## Dynamic url parameters

WMVC allows dynamic url parameters that will be transparent to Wordpress. You can achieve this by assigning an array as permalink parameter:

~~~ php
<?php
$app = array(
    "key" => "Mynamespace",
    "routes" => array(
        array('GET',        array('/songs/[*:slug]?/' => 'index.php?pagename=songs'),           'SongsController#view')
    )
);
?>
~~~

The array key is the permalink to match and the array value is the page Wordpress will have to load when it will hits that permalink. This allows you to fork all calls to one controller and one CMS page.

The route in the previous exemple will forward calls to `/songs/`, `/songs/my-name-is-jonas/`, `/songs/x-y-u/` to the method `view` of the controller `SongsController` with the matched slug as first parameter, should it be present.

As far as Wordpress will be concerned, the CMS page with the root permalink of `/songs/` will always be loaded because we've map the route to `index.php?pagename=songs`. This must be a valid second parameter to Wordpress' `add_rewrite_rule` function.

~~~ php
<?php
namespace Mywebsite\Controllers;

use MVC\Controller;
use Mywebsite\Models\Song;

class SongsController extends Controller {
    public function view($songSlug = null)
    {
        if (!is_null($songSlug)) {
            $this->set("song", Song::findBySlug($songSlug));
        }
    }
}
?>
~~~

## On-demand routing and dynamic callbacks

Should you wish to hook into one of Wordpress' hooks instead of an URL, you may need to generate a dynamic callback to a controller's action. You may call the `Router` directly to obtain a callback array.

~~~ php
<?php
    add_filter('pre_get_posts', \MVC\Router::callback('CallbackController', 'onPreGetPosts'));
?>
~~~

The previous example will call the method `onPreGetPosts` of your project's `CallbackController` controller class and send the expected function parameters.


~~~ php
<?php
namespace Mywebsite\Controllers;

use MVC\Controller;

class CallbackController extends Controller {
    public function onPreGetPosts($query)
    {
        // Manipulate the query.
    }
}
?>
~~~


## More complex request matching

Under the hood Wordpress MVC uses AltoRouter. As long as you enter the rules using their array notation, it will work. More information about how you can customize the rules can be found on [Altorouter's documention page](https://github.com/dannyvankooten/AltoRouter).

## On translated pages

To be as unobstrusive as possible we chose not to support automatic page translations mapping. However you can map multiple urls to the same controller, therefore you can expect the following example to work the way you would think :

~~~ php
<?php
$app = array(
    "key" => "Mynamespace",
    "routes" => array(
        array('GET|POST',   '/en/original-english-url/',     'HelloworldController#view')
        array('GET|POST',   '/fr/traduction-francaise/',     'HelloworldController#view')
    )
);
?>
~~~

## On Models url_rewrite

Url rewrites that are being added when creating custom post types are never taken into account when routing towards a controller file. In other words if the MVC has generated a post type that adds its own rewrite rule (ex: Song created the url_rewrite "/local-songs/"), the frameworks does not automatically map this new route to an implicit controller object. One would have to add such a rule manually.
