---
layout: docs
title: Installation
permalink: /docs/installation/
---

## Requirements

- A *nix enviroment (Unless you contribute to the project and help me create the .bat file)
- You require [VirtualBox && Extension Pack](https://www.virtualbox.org/wiki/Downloads)
- You require [Vagrant](http://www.vagrantup.com/downloads) to be installed
- You require [Node.js](https://nodejs.org/), NPM and [yo](https://github.com/yeoman/yo) to be installed.

Finally, you need to Wordpress MVC generator for Yeoman:

~~~ bash
$ npm install -g generator-wordpress-mvc
~~~


## Building the environment

Simply run the generator from the project's website. It will download the latest version of Wordpress as well as additional dependencies while creating the directory structure and configuration files for you.

~~~ bash
$ mkdir my-website && cd my-website
$ yo wordpress-mvc
~~~

The generator will confirm that you are in the correct directory before doing anything. Afterwards, it will ask you for the project's namespace that will be used across the project to identify your files.


## Kickstarting in Wordpress themes

To kickstart WMVC, open your current theme's `functions.php` file and include the bootstraper.

We encourage placing the include call in `functions.php` for consistency across projects. In reality, the actual place or method you use to include the file does not really matter as long as it precedes view files.

By default, our Yeoman generator includes the following line automatically to the default theme's `functions.php` file. You will have to add it manually when creating additionnal themes.

~~~ php
<?php
/* Load up MVC bootstrapper for wordpress */
\MVC\Mvc::bootstrap();
?>
~~~
