---
layout: guidebook
title: Generating Strata Classes
permalink: /guidebook/1.0/digging-in/shell/generating-strata-classes/
menu_group: shell
---

You should not be creating the files by hand when building a Strata app. We have a suite of commands that does repetitive actions for you all the while ensuring conventions are being enforced.

## Controller

Generates a new Controller class.

{% highlight bash linenos %}
$ ./strata generate controller MyController
{% endhighlight %}

## Model

Generates a new model class.

{% highlight bash linenos %}
$ ./strata generate model MyModel
{% endhighlight %}

## Custom Post Type

Generates a new custom post type model class.

{% highlight bash linenos %}
$ ./strata generate customposttype MyModel
{% endhighlight %}

## Taxonomy class

Generates a new taxonomy model class.

{% highlight bash linenos %}
$ ./strata generate taxonomy MyClass
{% endhighlight %}

## View helper class

Generates a new view helper class.

{% highlight bash linenos %}
$ ./strata generate helper MyClass
{% endhighlight %}

## Validator class

Generates a new form validator class.

{% highlight bash linenos %}
$ ./strata generate validator MyClass
{% endhighlight %}

## CLI command

Generates a new shell command class.

{% highlight bash linenos %}
$ ./strata generate command MyClass
{% endhighlight %}
