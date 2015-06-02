---
layout: docs
title: Object generator
permalink: /docs/shell/generator/
---

You should not be creating the files by hand when building a Strata app. We have a suite of commands and will do repetitive actions for you while ensuring that conventions are being enforced.


## Controller

Generates a new Controller class.

~~~ sh
$ bin/strata generate controller MyController
~~~

## Model

Generates a new model class.

~~~ sh
$ bin/strata generate model MyModel
~~~

## Custom Post Type

Generates a new custom post type model class.

~~~ sh
$ bin/strata generate customposttype MyModel
~~~

## Taxonomy class

Generates a new taxonomy model class.

~~~ sh
$ bin/strata generate taxonomy MyClass
~~~

## Form object

Generates a new form class.

~~~ sh
$ bin/strata generate form MyForm
~~~

## View helper class

Generates a new view helper class.

~~~ sh
$ bin/strata generate helper MyClass
~~~

## Validator class

Generates a new form validator class.

~~~ sh
$ bin/strata generate validator MyClass
~~~

## CLI command

Generates a new shell command class.

~~~ sh
$ bin/strata generate command MyClass
~~~

## Route

Generates a new route.

~~~ sh
$ bin/strata generate route GET|POST /[.*] AppController#index
~~~
