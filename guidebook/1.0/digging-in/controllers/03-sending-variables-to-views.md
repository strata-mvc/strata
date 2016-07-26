---
layout: guidebook
title: Sending variables to views
permalink: /guidebook/1.0/digging-in/controllers/sending-variables-to-views/
covered_tags: controller, views, variables
menu_group: controllers
---

A `View` object is associated to the active Controller object each time a route is executed. It is the interface that handles how variables are passed from Controllers to Wordpress templates.

To expose a variable and make it available to the regular Wordpress templating use the View object's `set($key, $mixed)` method. This will globally expose a variable named `$key` having a value of `$mixed` to the templates.

In the controller :

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
    $this->view->set("song", $mysong);
?>
{% endhighlight %}
{% include terminal_end.html %}

In a template file :

{% include terminal_start.html %}
{% highlight php linenos %}
<?php if (isset($song)) : ?>
    <p><?php echo $song->post_title; ?></p>
<?php endif; ?>
{% endhighlight %}
{% include terminal_end.html %}
