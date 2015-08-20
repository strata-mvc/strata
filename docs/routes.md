---
layout: docs
title: Routes
permalink: /docs/routes/
---

## Configuring

Routing rules are declared inside `/config/strata.php`. They are tested in the order that they appear in the configuration file.

There are 3 types of routes :

* Resourced-based routing
* Matched routing
    * Dynamically matched routing
* Callback routing

## Resourced-based routing

When you have created [Custom Post Types](/docs/models/customposttypes/), you may do automated routing on the object by setting the `routed` attribute to `true` in the model's declaration. Because the routing is implicitly declared, it does not need to be added to `/config/strata.php`.

~~~ php
<?php
namespace App\Model;

class Poll extends AppCustomPostType {

    public $routed = true;

    //...

}
?>
~~~

This will create 2 routes : one pointing to `PollController::index()` and a second one pointing to the `PollController::show($slug)` action.

The actual matched URL is decided by the `slug` key of the `rewrite` setting of the Custom Post Type's `$configuration` array. Should it not have been customized, it will try to match using the unique key used when registering the post type in Wordpress, which in this case would be `cpt_poll/` and `cpt_poll/[.*]/`.

## Matched routing

Matched routing occurs when URLs are compared to an exact string and are manually routed to a Controller and action destination.

This type of route is represented by an array consisting of 3 indexes :

* The supported __request type__
* The actual __permalink__ to match
* The destination __controller__ object and function.

~~~ php
<?php
$strata = array(
    "routes" => array(

        array('POST',       '/wp/wp-admin/admin-ajax.php',  'AjaxController'),
        array('GET|POST',   '/company/',                    'CompanyController#index'),
        array('GET|POST',   '/2014/12/hello-world/',        'HelloworldController#show'),

    )
);
?>
~~~

## Dynamically matched routing

You can dynamically declare controllers and actions using regular expression keys in the permalink. If you go as far as dynamically setting the controller and action, you no longer have to set the third destination parameter.

Note that it is the parsing of the route that is dynamic and not the creation of the page. Wordpress needs to know the URL exists as post or page slug. Otherwise you will always be forwarded to a 404.

~~~ php
<?php
$strata = array(
    "routes" => array(
        array('GET', '/about-us/team/[:slug]/', 'TeamController#show'),
        array('GET|POST|PATCH|PUT|DELETE', "/([:controller]/([:action]/([:params]/)?)?)?"),
    )
);
?>
~~~

You can catch URL parameters to variables with the use of `[*:varname]`. A variable caught that way would then be available as first parameter of the callback function inside the controller.

~~~ php
<?php
$strata = array(
    "routes" => array(
        array('GET',        '/music-page/',                 'SongController#index'),
        array('GET',        '/music-page/[*:slug]/',        'SongController#show'),
    )
);
?>
~~~

The last route in the previous example will trigger on calls to `/music-page/my-name-is-jonas/` and `/music-page/x-y-u/`. These request will be routed to the `show` method of the controller `SongController` with the matched `slug` as first parameter.

~~~ php
<?php
namespace App\Controller;

use App\Model\Song;

class SongController extends AppController {

    public function index()
    {

    }

    public function show($slug = null)
    {
        if (!is_null($slug)) {
            $this->set("song", Song::repo()->findBySlug($slug));
        }
    }

}
?>
~~~


### More complex request matching

Under the hood Strata uses AltoRouter to match routes. As long as you specify rules using their array notation it will work. More information about how you can customize the rules can be found on [Altorouter's documention page](https://github.com/dannyvankooten/AltoRouter).

<!--
## Creating a new route

To generate a route, you should use the automated generator provided by Strata. It will ensure it will be correctly defined.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a route to `SongController`'s `view()` function.

~~~ sh
$ ./strata generate route 'GET' '/music-page/[*:slug]/' 'SongController#view'
~~~

-->

## Callback routing

Quite frequently, you will want to hook into Wordpress using `add_filter` and `add_action`. For these hooks to refer to your controllers, you need to generate a dynamic callback to a controller's action. You may call the `\Strata\Router\Router` object directly to obtain a callback array.

~~~ php
<?php
// function.php
    add_filter('pre_get_posts', \Strata\Router\Router::callback('CallbackController', 'onPreGetPosts'));
?>
~~~

The previous example will call the method `onPreGetPosts` of your project's `CallbackController` controller class and send the expected function parameters.

~~~ php
<?php
namespace App\Controller;

class CallbackController extends AppController {
    public function onPreGetPosts($query)
    {
        // Manipulate the query.

        return $query;
    }
}
?>
~~~

### Template routing

You can route by templates by adding a call to gain a dynamic callback and running in at the top of your template file.

So, a `template-song.php` template file could look like the following :

~~~ php
<?php
/*
Template Name: Song Page Template
*/
?>

<?php
    # Call a controller#action
    $callback = \Strata\Router\Router::callback('SongController', 'index');
    $callback();
?>

<?php get_template_part('templates/content', 'song'); ?>
~~~


## On translated pages and common destinations

You can map multiple URLs to the same controller, therefore you can expect the following example to work the way you would think :

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
