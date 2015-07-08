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
 - Active Internet connection while running install scripts

## One step install

The following steps are all contained in a script that can be ran form an online location. There are security implications because there is no signed validation, but these should be notable for most use-cases.

~~~ bash
$ bash <(curl -s http://create-strata-project.francoisfaubert.com)
~~~

## Manual installation

### Install Composer

Strata uses [Composer](http://getcomposer.org/) to manage it's dependencies. You must therefore have Composer installed on your machine.

### Install Strata

If you are starting from scratch you will need to create a new Bedrock project, on which Strata is based.

~~~ bash
$ composer create-project roots/bedrock MyApplication
~~~

From the project directory, add Strata as a dependency.

~~~ bash
$ composer require francoisfaubert/strata:dev-master
~~~

There are folders and files that need to be created in order for Strata to be used from within your project. To ensure you have everything needed, the last step is to run the Strata installer script which comes packaged in Strata.

Ensure the file is executable first, then run the bash script. The script will confirm that you have the correct directory structure as well as populate your project with starter files. Note that it will also delete some of the unneeded files that were added by Bedrock.

~~~ bash
$ chmod +x vendor/francoisfaubert/strata/src/Scripts/install
$ vendor/francoisfaubert/strata/src/Scripts/install
~~~

## Wordpress hooks

During the installation phase, Strata will include a new must-use plugin named `web/app/mu-plugin/strata-bootstraper.php`. It is this plugin that will ensure Strata is automatically registered upon each page load.

## Rebuilding a Strata project

If you need to rebuild the project structure, whether because of how the project is versioned or simply because symbolic links are missing, you can run the packaged bootstrapping script. It checks that Strata is properly configured in the current state.

~~~ bash
$ chmod +x vendor/francoisfaubert/strata/src/Scripts/bootstrap
$ vendor/francoisfaubert/strata/src/Scripts/bootstrap
~~~
