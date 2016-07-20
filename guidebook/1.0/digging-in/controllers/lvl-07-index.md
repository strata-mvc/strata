---
layout: guidebook
title: Controllers
permalink: /guidebook/1.0/digging-in/controllers/
entry_point: true
menu_group: controllers
group_label: Controllers
group_theme: Digging In
---

Controllers represent entry points that make sense for humans beings in modern web applications usually built from the possible application URLs. Once you have set up at least one [application route](/guidebook/1.0/digging-in/routing/) in your project's [configuration file](/guidebook/1.0/getting-started/creating-projects/configuring-for-installation/) you must build the corresponding controller endpoints.

The main use case of Controllers in Strata is to replace the need of placing pure code in template file. Instead of instantiating queries and various variables inside a template file, you should place the code in a Controller. The biggest and most obvious gain is the ability to use the same business logic code multiple times as well as improving code testability.

## Creating a controller file

To generate a Controller, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined following the guidelines.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a controller for the `Artist` object:

{% highlight bash linenos %}
$ ./strata generate controller artist
{% endhighlight %}

It will generate a couple of files for you, including the actual controller file and test suites for the generated class.

{% highlight bash linenos %}
Scaffolding controller ArtistController
  ├── [ OK ] src/controller/ArtistController.php
  └── [ OK ] test/controller/ArtistControllerTest.php
{% endhighlight %}
