---
layout: docs
title: Documentation
permalink: /docs/
---

## Why

Wordpress is a fantastic blog engine and can sometimes be used as a full fledged CMS. When building more complex websites and applications, one may find that you can't quite cleanly separate the business logic from the templating engine. For this reason, WMVC tries to organize your business code away from the templates.

## What it does

Packaged in a stand-alone developing environment similar to CakePHP and Rails', Wordpress MVC allows you to dissociate business logic code form the template files while maintaining a totally clean and valid Wordpress installation.

You can associate permalinks to controller classes to offer an elegant way of maintaining complex code and keep the templates as clean as possible. It also maps models to custom post types so model relationships can be programmed similarly elegantly.

It routes dynamic callback to controllers and removes all application logic from the theme's templates files without affecting the normal Wordpress flow.

## What it doesn't do

We do not want to change how Wordpress works. Integrators and front-end programmers should not notice what is being processed in the controllers except for explicitly exposed shortcodes and view variables. We also do not want to interfere with core Wordpress variables, libraries, plugins and themes or custom posts types created outside the MVC process.

