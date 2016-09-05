---
layout: guidebook
title: Creating Projects
permalink: /guidebook/1.0/getting-started/creating-projects/
entry_point: true
menu_group: creating-projects
group_label: Creating Projects
group_theme: Getting Started
---

It is important to visualize that a Strata project is composed of two different entities:

* **strata-mvc/project** : A Strata project's file structure. It is the empty shell in which you will add your application's code.
* **strata-mvc/strata** : The Model-View-Controller library itself that is loaded as a dependency in the Strata Environment.

## Generating an environment

Use Composer's `create-project` command to create a new blank Strata project. The starter project package is named `francoisfaubert/strata-env` and you must specify the final directory as second parameter.


{% include terminal_start.html %}
{% highlight bash %}
$ composer create-project strata-mvc/project MyApplication
$ cd MyApplication
{% endhighlight %}
{% include terminal_end.html %}

After the command will have completed you will be with a project that is not entirely installed. Your project's theme and any PHP dependencies still need to be added and installed.
