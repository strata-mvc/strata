---
layout: guidebook
title: Callback routing
permalink: /guidebook/1.0/digging-in/routing/callback-routing/
menu_group: routing
---

Quite frequently, you will want to hook into Wordpress using `add_filter` and `add_action`. For these hooks to refer to your controllers, you need to generate a dynamic callback to a controller's action. You may call the `\Strata\Router\Router` object directly to obtain a callback array.

The `callback()` function requires 2 parameters:

* A controller's short name
* An action

To illustrate how you would do so in your active theme, the following could be placed in `~/web/app/themes/you-theme/functions.php`:

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
    add_filter('pre_get_posts', \Strata\Router\Router::callback('CallbackController', 'onPreGetPosts'));
?>
{% endhighlight %}
{% include terminal_end.html %}

The previous example will call the method `onPreGetPosts` of your project's `CallbackController` controller class and send the expected function parameters as documented by the Wordpress filter or action.

The controller endpoint would look like the following:

{% include terminal_start.html %}
{% highlight php linenos %}
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
{% endhighlight %}
{% include terminal_end.html %}
