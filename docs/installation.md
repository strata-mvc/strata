---
layout: docs
title: Installation
permalink: /docs/installation/
---

## One step install

The following steps are all contained in a script that can be ran form an online file. Make sure you know the security implications, but it makes the process simpler.

~~~ bash
$ bash <(curl -s https://raw.githubusercontent.com/francoisfaubert/strata/master/src/Scripts/create_project)
~~~

## Manual installation

### Install Composer

Strata is a dependency of [Bedrock](https://roots.io/bedrock/), which uses [Composer](http://getcomposer.org/) to manage its dependencies.

You need Composer installed on your machine before using Strata and Bedrock.

### Install Strata

If you are starting from scratch, you will need to create a new Bedrock project.

~~~ bash
$ composer create-project roots/bedrock mywebsite
~~~

From the project directory, add the Strata as a dependency.

~~~ bash
$ composer require francoisfaubert/strata:dev-master
~~~

There are some folders and files that need to be created in order for Strata to be used inside your project. To ensure you have everything needed, the last step is to run the Strata installer script which comes packaged in Strata.

Ensure the file is executable first, then run the bash script. The script will confirm that you have the correct directory structure as well as populate your project with starter files.

~~~ bash
$ chmod +x vendor/francoisfaubert/strata/src/Scripts/install
$ vendor/francoisfaubert/strata/src/Scripts/install
~~~

## Wordpress hooks

During the installation phase, Strata will include a new must-use plugin named `web/app/mu-plugin/strata-bootstraper.php`. It is this plugin that will ensure Strata is automatically loaded upon each page load.

## Requirements

{% include workinprogress.html %}

Strata means to be as self-contained as possible. It achieves this be packaging all of the tools it needs within the project. With that in mind, the most self-contained way it can be used is within a Vagrant machine which will contain the required PHP and MySQL installation.

Should you think this is unnecessary overhead, you can disable the Vagrant virtual machine and use the PHP and MySQL binaries available to your computer. One can therefore use a PHP binary supplied by MAMP by making their php binary available to your PATH variable.

In other words, the requirements the purely **stand-alone mode** are :

 - Vagrant
 - VirtualBox

The requirements for **local machine mode** are :

 - PHP >= 5.3 (available to your $PATH)
 - Mcrypt PHP Extension
 - MySQL

