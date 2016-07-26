---
layout: guidebook
title: Controllers
permalink: /guidebook/1.0/digging-in/controllers/
entry_point: true
menu_group: controllers
group_label: Controllers
group_theme: Digging In
---

Controllers are entry points into your application that make sense for humans beings in modern web applications. The way of routing request to a Controller is usually by analyzing the application URL for each request.

The main use case of Controllers in Strata is to replace the need of placing pure code in template file. Instead of instantiating queries and various variables inside a template file, you should place this code in a Controller. The biggest and most obvious gain is the ability to use the same business logic code multiple times as well as improving code testability and readability.

Once you have set up at least one [application route](/guidebook/1.0/digging-in/routing/) in your project's [configuration file](/guidebook/1.0/getting-started/creating-projects/configuring-for-installation/) you must build the corresponding controller endpoints to complete the process.

## Creating a controller file

To generate a Controller, you should use the automated generator provided by Strata. It will validate your object's name and ensure it is defined following the intended conventions.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a controller for the `Artist` object:

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate controller artist
{% endhighlight %}
{% include terminal_end.html %}

The command generates a couple of files for you, including the actual Controller file and test suites for the generated class.

{% include terminal_start.html %}
{% highlight bash linenos %}
Scaffolding controller ArtistController
  ├── [ OK ] src/Controller/ArtistController.php
  └── [ OK ] test/Controller/ArtistControllerTest.php
{% endhighlight %}
{% include terminal_end.html %}
