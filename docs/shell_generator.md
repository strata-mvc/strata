---
layout: docs
title: Object generator
permalink: /docs/shell/generator/
---

You should not be creating the files by hand when building a Strata app. We have a suite of commands that does repetitive actions for you all the while ensuring conventions are being enforced.


## Controller

Generates a new Controller class.

~~~ sh
$ ./strata generate controller MyController
~~~

## Model

Generates a new model class.

~~~ sh
$ ./strata generate model MyModel
~~~

## Custom Post Type

Generates a new custom post type model class.

~~~ sh
$ ./strata generate customposttype MyModel
~~~

## Taxonomy class

Generates a new taxonomy model class.

~~~ sh
$ ./strata generate taxonomy MyClass
~~~

## View helper class

Generates a new view helper class.

~~~ sh
$ ./strata generate helper MyClass
~~~

## Validator class

Generates a new form validator class.

~~~ sh
$ ./strata generate validator MyClass
~~~

## CLI command

Generates a new shell command class.

~~~ sh
$ ./strata generate command MyClass
~~~

<!--
## Route

Generates a new route.

~~~ sh
$ ./strata generate route GET|POST /[.*] AppController#index
~~~
-->
