---
layout: guidebook
title: Creating a Middleware
permalink: /guidebook/1.0/digging-in/middlewares/creating-a-middleware/
menu_group: middlewares
---

To generate a `Middleware`, you should use the automated generator provided by Strata. It will validate your object's name and ensure it is defined following the intended conventions.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a middleware for IP forwarding:

{% highlight bash linenos %}
$ ./strata generate middleware IpForwarding
{% endhighlight %}

The command generates a couple of files for you, including the actual `Middleware` initializer file and test suites for the generated class.

{% highlight bash linenos %}
Scaffolding controller ArtistController
  ├── [ OK ] src/Middleware/IpForwardingInitializer.php
  └── [ OK ] test/Middleware/IpForwardingInitializerTest.php
{% endhighlight %}
