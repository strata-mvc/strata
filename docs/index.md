---
layout: docs
title: Manifest
permalink: /docs/
---

## Why

Wordpress is a fantastic blog engine that can sometimes be used as a full fledged CMS for complex applications. In these situations, one may find that you can't quite cleanly separate the business logic from the templating engine. For this reason, Strata tries to organize your business code outside the templates.

## What it does

Packaged as a stand-alone developing environment similar to CakePHP and Rails', Strata allows you to dissociate business logic code from the template files while maintaining a clean and valid Wordpress installation.

You can declare routes that will call Controller classes and offer an elegant way of maintaining complex code. Complex querying and entity attributes are handled by Models and Model Entities. The template files are therefore only used to present prepared data.

All of this without affecting the normal Wordpress flow and plugin integration.

## What it doesn't do

Strata does not want to change how Wordpress works. Integrators and front-end programmers should not notice what is being processed in the controllers except for explicitly exposed shortcodes and view variables. Strata do not interfere with core Wordpress variables, libraries, plugins, themes or custom posts types created outside it's MVC setup.

This implies Strata works best outside of the Wordpress loop and in moment when Wordpress needs to convey information that does not fit the blogging model.

Think of Strata not as _another way_ of doing things, but rather as _another place_ where you do these things.
