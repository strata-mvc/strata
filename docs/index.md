---
layout: docs
title: Manifest
permalink: /docs/
---

## Why

Wordpress is a fantastic blog engine and can sometimes be used as a full fledged CMS. When building more complex websites and applications, one may find that you can't quite cleanly separate the business logic from the templating engine. For this reason, Strata tries to organize your business code away from the templates.

## What it does

Packaged as a stand-alone developing environment similar to CakePHP and Rails', Strata allows you to dissociate business logic code from the template files while maintaining a clean and valid Wordpress installation.

You can declare routes that will call Controller classes and offer an elegant way of maintaining complex code. The template file will be used only to present data while the business logic is moved into Models. It also maps Models to Custom Post Types allowing Model relationships and inheritance that can be programmed elegantly.

All of this without affecting the normal Wordpress flow.

## What it doesn't do

We do not want to change how Wordpress works. Integrators and front-end programmers should not notice what is being processed in the controllers except for explicitly exposed shortcodes and view variables. We also do not want to interfere with core Wordpress variables, libraries, plugins, themes or custom posts types created outside the MVC process.

Think of Strata not as _another way_ of doing things, but rather as _another place_ where you do these things.
