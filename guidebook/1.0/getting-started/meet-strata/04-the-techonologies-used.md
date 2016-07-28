---
layout: guidebook
title: The Technologies Used
permalink: /guidebook/1.0/getting-started/meet-strata/the-technologies-used/
covered_tags: installation
menu_group: meet-strata
---

Strata uses [Composer](https://getcomposer.org/) to fetch Wordpress, the plugins and required PHP libraries at specified versions.

The framework is tested to ensure smooth performance on PHP 5.4, 5.6, 7.0 and HHVM.

## Requirements

Strata requires a *nix based environment with Linux and Mac support being actively tested. Support for Windows environments is currently not tested but good mileage may be achieved using wrappers like Cygwin.

* PHP >= 5.4
  * Mysqli
  * Mbstring
* MySQL
* Composer

## Command line heavy

Most of the tools bundled in Strata are ran using the command line. At the moment the Windows environment is not officially supported. Most Linux and Mac environments will run Strata scripting without hiccups.

## Imposed limits

They may be other limitations imposed by Wordpress or installed plugins. Most will be easy to figure out but some may arise as a surprise as your project and its dependencies evolve.

For instance MySQL (or MariaDB) is a Wordpress requirement and you couldn't seamlessly replace it with SQlite when developing locally. (Though we have a plan.)
