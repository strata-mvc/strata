---
layout: guidebook
title: Sending variables to views
permalink: /guidebook/1.0/digging-in/controllers/sending-variables-to-views/
covered_tags: controller, views, variables
menu_group: controllers
---


On each instantiation of the Controller object, a linked View object is created. It is the interface that handles how generated content is handled.

To expose a variable and make it available to the regular Wordpress templating, use the class' `set($key, $mixed)` method. This will globally expose a variable named `$key` having a value of `$mixed` to the templates.

In the controller :

{% highlight php linenos %}
<?php
    $this->view->set("song", $mysong);
?>
{% endhighlight %}

In a template file :

{% highlight php linenos %}
<?php if (isset($song)) : ?>
    <p><?php echo $song->post_title; ?></p>
<?php endif; ?>
{% endhighlight %}
