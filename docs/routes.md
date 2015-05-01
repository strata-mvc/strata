---
layout: docs
title: Routes
permalink: /docs/routes/
---

## Configuring

Routes are declared inside `/config/strata.php`.

A routing rule is represented by an array consisting of 3 indexes :

* The supported __request type__
* The actual __permalink__ to match
* The destination __controller__ object and function.

You can pipe multiple request types -- useful when you have a form on the page -- to enforce how you handle callbacks.

~~~ php
<?php
$strata = array(
    "routes" => array(
        array('GET|POST',   '/2014/12/hello-world/',        'HelloworldController#view')
        array('POST',       '/wp-admin/admin-ajax.php',     'AjaxController')
    )
);
?>
~~~

## Creating a new route

To generate a route, you should use the automated generator provided by Strata. It will ensure it will be correctly defined.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a route to `SongController`'s `view()` function.

~~~ sh
$ bin/strata generate route 'GET' '/music-page/[*:slug]/' 'SongController#view'
~~~

## Dynamic url parameters

You can also automatically catch URL parameters to variables with the use of `[*:varname]`. A variable caught that way would then be available as first parameter of the callback function inside the controller.

~~~ php
<?php
$strata = array(
    "routes" => array(
        array('GET',        '/music-page/',                 'SongController#index'),
        array('GET',        '/music-page/[*:slug]/',        'SongController#view'),
    )
);
?>
~~~

The last route in the previous example will trigger on calls to `/music-page/my-name-is-jonas/`, `/music-page/x-y-u/` should the custom post types exist and be publicly visible to the frontend. These request will ensure a call to the `view` method of the controller `SongController` with the matched slug as first parameter.

~~~ php
<?php
namespace Mywebsite\Controller;

use Mywebsite\Model\Song;

class SongController extends \Mywebsite\Controller\AppController {
    public function view($songSlug = null)
    {
        if (!is_null($songSlug)) {
            $this->set("song", Song::findBySlug($songSlug));
        }
    }
}
?>
~~~

What we mean by mentioning the custom post type needs to be publicly visible to the frontend is that the custom post type `Song` needs to be configured with the proper rewrite rule prefix:

~~~ php
<?php
// src/Model/Song.php snippet
 public $configuration = array(
        'publicly_queryable' => true,
        "rewrite"   => array(
            'slug'                => 'music-page'
        )
    );
?>
~~~

## On-demand routing and dynamic callbacks

Quite frequently, you will want to hook into Wordpress using `add_filter` and `add_action`. For these hook to refer to your controllers, you will need to generate a dynamic callback to a controller's action. You may call the `\Strata\Router\Router` object directly to obtain a callback array.

~~~ php
<?php
    add_filter('pre_get_posts', \Strata\Router\Router::callback('CallbackController', 'onPreGetPosts'));
?>
~~~

The previous example will call the method `onPreGetPosts` of your project's `CallbackController` controller class and send the expected function parameters.

~~~ php
<?php
namespace Mywebsite\Controller;

class CallbackController extends \Mywebsite\Controller\AppController {
    public function onPreGetPosts($query)
    {
        // Manipulate the query.

        return $query;
    }
}
?>
~~~

## More complex request matching

Under the hood Strata uses AltoRouter. As long as you specify rules using their array notation it will work. More information about how you can customize the rules can be found on [Altorouter's documention page](https://github.com/dannyvankooten/AltoRouter).

## On translated pages

To be as unobtrusive as possible we chose not to support automatic page translations mapping. However you can map multiple URLs to the same controller, therefore you can expect the following example to work the way you would think :

~~~ php
<?php
$app = array(
    "routes" => array(
        array('GET|POST', '/en/original-english-url/', 'HelloworldController#view')
        array('GET|POST', '/fr/traduction-francaise/', 'HelloworldController#view')
        array('GET|POST', '/es/hola-amigo/', 'HelloworldController#view')
    )
);
?>
~~~

## Model Binding

URL rewrites that are being added when creating custom post types are never taken into account when routing towards a controller file.

In other words if Strata has generated a post type that adds its own rewrite rule (ex: Song created the url_rewrite "/local-songs/"), the framework does not automatically map this new route to an implicit controller object.

You always have to add rules yourself.
