---
layout: guidebook
title: Using the Bundled Server
permalink: /guidebook/1.0/digging-in/shell/bundled-server/
menu_group: shell
---

Strata can launch a server for you at [127.0.0.1:5454](http://127.0.0.1:5454/) by invoking the `server` command.

{% highlight bash linenos %}
$ ./strata server
{% endhighlight %}

Should you have a `php.ini` file located at the root of your project, Strata will launch the server by taking it into account.
