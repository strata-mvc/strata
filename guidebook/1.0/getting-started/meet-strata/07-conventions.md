---
layout: guidebook
title: Conventions
permalink: /guidebook/1.0/getting-started/meet-strata/conventions/

menu_group: meet-strata
---

Strata attempts to bridge [Wordpress' Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/) with PHP's [PSR-2](http://www.php-fig.org/psr/psr-2/) standard based on the context in which the code is used.

All PHP classes across the whole application should be written using PSR-2:

* Tests
* Controllers
* Models
* Helpers
* Commands

On the other hand, all templating files placed within the web-facing scope should use known Wordpress standards:

* Templates
* single-*.php
* Partials and Layouts
* Filter and action declarations
