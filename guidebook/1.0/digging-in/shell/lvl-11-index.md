---
layout: guidebook
title: Shell
permalink: /guidebook/1.0/digging-in/shell/
entry_point: true
menu_group: shell
group_label: Shell
group_theme: Digging In
---

Strata ships with multiple CLI tools that allow you to automate actions. It also allows you to create your own scripts that have access to your application's Models without the overhead of the web server.

The basic call to trigger the command line interface is:

{% highlight bash linenos %}
$ ./strata _command_ _arguments_
{% endhighlight %}
