---
layout: guidebook
title: Helpers
permalink: /guidebook/1.0/digging-in/helpers/
entry_point: true
menu_group: Helpers
group_label: Helpers
group_theme: Digging In
---

A Helper, or more precisely a `ViewHelper`, is a class that helps organize pure code from the context of pure template files by handling common and repetitive tasks. A use case for this could be a class that wraps the logic of thumbnail presentation for instance.

## Creating a Helper file.

To generate a `ViewHelper`, you should use the automated generator provided by Strata. It will validate your object's name and ensure it is defined following the intended conventions.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a view helper for the `Artist` object:

{% highlight bash linenos %}
$ ./strata generate helper Artist
{% endhighlight %}

The command generates a couple of files for you, including the actual `ViewHelper` file and test suites for the generated class.

{% highlight bash linenos %}
Scaffolding controller ArtistController
  ├── [ OK ] src/View/Helper/ArtistHelper.php
  └── [ OK ] test/View/Helper/ArtistHelperTest.php
{% endhighlight %}
