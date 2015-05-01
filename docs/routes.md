---
layout: docs
title: Routes
permalink: /docs/routes/
---

## Configuring

Routes are declared inside `app.php` found in `/config/`.

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
        array('POST',       '/wp-admin/admin-ajax.php',     'AjaxController#index')
        array('GET',        '/music-page/',                 'SongController#index'),
        array('GET',        '/music-page/[*:slug]/',        'SongController#view'),
    )
);
?>
~~~

In the previous example, you can see you can pipe multiple request types (useful when you have a form on the page). You can also automatically catch url parameters to variables with the use of `[*:varname]`.

## Dynamic url parameters

The last route in the previous example will trigger on calls to `/music-page/my-name-is-jonas/`, `/music-page/x-y-u/` should the custom post types exist and be publicly visible to the frontend. These request will  to the method `view` of the controller `SongController` with the matched slug as first parameter, should it be present.

The custom post type `Song` needs to be configured the proper rewrite rule prefix :

~~~ php
<?php
 public $configuration = array(
        'publicly_queryable' => true,
        "rewrite"   => array(
            'slug'                => 'music-page',
            'with_front'          => true,
        )
    );
?>
~~~

~~~ php
<?php
namespace Mywebsite\Controller;

use Mywebsite\Model\Song;

class SongController extends \MyProject\Controller\AppController {
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

Should you wish to hook into one of Wordpress' filters and action instead of an URL, you may need to generate a dynamic callback to a controller's action. You may call the `Router` object directly to obtain a callback array.

~~~ php
<?php
    add_filter('pre_get_posts', \Strata\Router::callback('CallbackController', 'onPreGetPosts'));
?>
~~~

The previous example will call the method `onPreGetPosts` of your project's `CallbackController` controller class and send the expected function parameters.

~~~ php
<?php
namespace Mywebsite\Controller;

class CallbackController extends \MyProject\Controller\AppController {
    public function onPreGetPosts($query)
    {
        // Manipulate the query.
    }
}
?>
~~~

## More complex request matching

Under the hood Strata uses AltoRouter. As long as you enter the rules using their array notation, it will work. More information about how you can customize the rules can be found on [Altorouter's documention page](https://github.com/dannyvankooten/AltoRouter).

## On translated pages

To be as unobtrusive as possible we chose not to support automatic page translations mapping. However you can map multiple urls to the same controller, therefore you can expect the following example to work the way you would think :

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
