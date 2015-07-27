---
layout: docs
title: Installation
permalink: /docs/installation/
---

## Requirements

 - Linux, Mac OS, Cygwin (untested)
 - PHP >= 5.3
 - MySQL
 - Composer

## Installation

### Install Composer

Strata uses [Composer](http://getcomposer.org/) to manage it's dependencies. You must therefore have Composer installed on your machine.

### Creating the project

Use Composer's default `create-project` command to create a new empty Strata project. The empty template package is named `strata-env` and you must specify the final directory as second parameter.

~~~ bash
$ composer create-project francoisfaubert/strata-env MyApplication
~~~

## Rebuilding or repairing a Strata project

Should your installation be broken, Strata bundles a script that will check if all the dependencies are met.

From the root of the project, use `env repair` to inspect the project.

~~~ bash
$ strata env repair
~~~
