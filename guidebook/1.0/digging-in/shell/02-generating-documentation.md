---
layout: guidebook
title: Generating Documentation
permalink: /guidebook/1.0/digging-in/shell/generating-documentation/
menu_group: shell
---

Strata offers means for an application to be self maintained. This implies that it must also be built with customized documentation about the implementation so that another programmer (or you, next month) can have a high level view of the code base.

To generate your app's documentation, run `./strata document` from the base of your website. The documentation will be generated to the `doc` directory.

The documentation files are static and can therefore be loaded directly in your browser without launching a web server.

The script will generate 2 types of documentations :

* An API of the classes in `/src/`
* An overview of what is being customized in your theme's directory
