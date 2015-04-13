---
layout: docs
title: Documentation
permalink: /docs/
---

## Why

Wordpress is a fantastic blog engine and can sometimes be used as a full fledged CMS. When building more complex websites and applications, one may find that you can't quite cleanly separate the business logic from the templating engine. For this reason, WMVC tries to organize your business code away from the templates.

## What it does

Packaged in a stand-alone developing environment similar to CakePHP and Rails', Wordpress MVC allows you to dissociate business logic code form the template files while maintaining a totaly clean and valid Wordpress installation.

You can associate permalinks to controller classes to offer an elegant way of maintaining complex code and keep the templates as clean as possible. It also maps models to custom post types so model relationships can be programmed similarly elegantly.

It routes dynamic callback to controllers and removes all application logic from the theme's templates files without affecting the normal wordpress flow.

## What it doesn't do

We do not want to change how Wordpress works. Integrators and front-end programmers should not notice what is being processed in the controllers except for explicitly exposed shortcodes and view variables. We also do not want to interfere with core Wordpress variables, libraries, plugins and themes or custom posts types created outside the MVC process.

## The state of the project

Bottom line : not yet ready to be used. However it should be ready "pretty soon" as I am solving issues daily on the project.

It is honestly still really early in the process of creating this tool but things move fast and convincingly. This means that it is quite possible there are issues when you are trying out the solutions.

Additionally, the documentation is not written with as much care as it deserves. I am french-speaking and writing in English is harder for me, both syntaxtically and in finding typos. I am also the one that coded everything with means I may not be explaining the things other developpers want to know.

I am comitted in shipping a working release very soon as well as exposing the API and proper documentation. In the meantime, if you encounter spelling mistakes or inconsistencies in the code, please report them to me on GitHub (or even better contribute to the project!)

Here is my unofficial todo list, vaguely in the order which I plan to go by:

* While code is close to being in final condition, I am still finalizing concepts. Some of these concepts are not completed across the board (singular vs plural names, base classes inside vs base classes outside of descendent directory). This implies code that will be moved around.
* Complete the list of default generators for bin/mvc
* Improve the Vagrant machine to prevent weird bugs (no constant logging, clock out of sync, menu on reboot)
* Create a bin/mvc.bat counterpart for Windows
* Add a testing suite
* Add a documentor
* Do screen casts
