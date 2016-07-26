---
layout: guidebook
title: Generating Strata Classes
permalink: /guidebook/1.0/digging-in/shell/generating-strata-classes/
menu_group: shell
---

You should not be creating the files by hand when building a Strata app. We have a suite of commands that does repetitive actions for you all the while ensuring conventions are being enforced.

## Controller

Generates a new Controller class.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate controller MyController
{% endhighlight %}
{% include terminal_end.html %}

## Model

Generates a new model class.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate model MyModel
{% endhighlight %}
{% include terminal_end.html %}

## Custom Post Type

Generates a new custom post type model class.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate customposttype MyModel
{% endhighlight %}
{% include terminal_end.html %}

## Taxonomy class

Generates a new taxonomy model class.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate taxonomy MyClass
{% endhighlight %}
{% include terminal_end.html %}

## View helper class

Generates a new view helper class.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate helper MyClass
{% endhighlight %}
{% include terminal_end.html %}

## Validator class

Generates a new form validator class.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate validator MyClass
{% endhighlight %}
{% include terminal_end.html %}

## CLI command

Generates a new shell command class.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate command MyClass
{% endhighlight %}
{% include terminal_end.html %}
