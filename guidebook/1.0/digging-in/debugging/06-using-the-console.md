---
layout: guidebook
title: Using the console
permalink: /guidebook/1.0/digging-in/debugging/using-the-console/
covered_tags: development, console
menu_group: debugging
---

Should you wish to debug areas of your code that do not require visual output from an HTTP server, you may want to test using the bundled Console. This is one of the most efficient way of testing Query chaining within Strata.

From the root of your project, launch a new Console using the command line interface:

{% highlight bash linenos %}
$ ./strata console
{% endhighlight %}

From there you may call your models and test which values they may return:

![Console to test queries](/assets/images/console-sample.png)
