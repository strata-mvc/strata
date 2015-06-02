---
layout: docs
title: Documentation
permalink: /docs/shell/documenting/
---

Strata offers means for an application to be self maintained. This implies that it must also be packaged by documentation about the implementation so that another programmer (or you, next month) can have a high level view of the code base.

To generate your app's documentation, run `bin/strata document` from the base of your website. The documentation will be output in the `doc` directory.

The documentation files are static and can therefore be loaded directly in your browser without launching a web server.

The script will generate 2 types of documentations :

* The API of the classes in `/src/`
* An overview of what is being customized in your theme's directory
