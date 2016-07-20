---
layout: guidebook
title: Resource routing
permalink: /guidebook/1.0/digging-in/routing/resource-routing/
menu_group: routing
---

When you have created [Custom Post Types](/guidebook/1.0/digging-in/models/custom-post-types/) through Strata you may do automated routing on the object by configuring it's `routed` attribute. Because this type of routing is implicitly declared, it does not need to be added to `/config/strata.php` the way a matched permalink route would have had to.

## Basic usage

The simplest way of enabling routes on a Model is to set its `$routed` attribute to `true` :

{% highlight php linenos %}
<?php
namespace App\Model;

class Poll extends AppCustomPostType {

    public $routed = true;

    //...

}
?>
{% endhighlight %}

This will automatically declare 2 routes : the first pointing to `PollController::index()` and the second pointing to the `PollController::show($slug)` action.

The actual matched URL is decided by the `slug` key of the `rewrite` setting of the Custom Post Type's `$configuration` array. Should it not have been customized, it will try to match using the unique key used when registering the post type in Wordpress, which in this case would be `cpt_poll/` and `cpt_poll/[.*]/`.

## Customizing the destination

If you wish to change the destination of a resourceful route, you may pass a configuration array to `$routed` instead of a boolean.

The following configuration will send all requests that match a request the this type of custom post type's (think `single-*.php`) to `BusinessController#sendQuote($slug)`.

{% highlight php linenos %}
<?php
namespace App\Model;

class Poll extends AppCustomPostType {
    public $routed = array(
        "controller" => "App\Controller\BusinessController",
        "action" => "sendQuote",
    );

?>
{% endhighlight %}

## Automated trailing URL parts

You may want to have additional unique URLs that eventually point to the same custom post type object. Take for instance a contact form that would be on a page titled `Contact` with `/contact/` as permalink. A good practice is to have form submit to a different URL for processing. In this case the model used to generate the form needs to declare this new URL and make sure Wordpress will know to map it to `/contact/`.

Additional model urls parts are defined under `rewrite`. The following configuration will allow `/contact/` and `/contact/send-my-form` to coexist.

{% highlight php linenos %}
<?php
namespace App\Model;

class ContactFormApplication extends AppModel
{
    public $routed = array(
        "rewrite" =>  array(
            'send' => 'send-my-form',
        ),
    );
}
?>
{% endhighlight %}

## Customizing match rules

All the previous examples are expected to be ran on object inheriting from `AppCustomPostType`. You may hard-code routes on `AppModel` objects should you need to map a destination explicitly. This becomes useful when matching model concepts against loose pages.

This class representing a form for a quote on a custom build of a product does not map to posts in Wordpress and therefore cannot imply routing rules. However by setting the `page_slug_regex` key it enables a route to `BusinessController::sendQuote()` when the current post's permalink matches `business/get-a-quote`.

{% highlight php linenos %}
<?php
namespace App\Model;

class CustomSystemApplication extends AppModel
{
    public $routed = array(
        "controller" => "App\Controller\BusinessController",
        "action" => "sendQuote",
        "page_slug_regex" => "(business/get-a-quote)",
        "rewrite" =>  array(
            'send_quote' => 'send-quote',
        ),
    );
}
?>
{% endhighlight %}
