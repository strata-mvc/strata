---
layout: guidebook
title: How it integrates with Wordpress
permalink: /guidebook/1.0/getting-started/meet-strata/how-it-integrates-with-wordpress/

menu_group: meet-strata
next_page: /guidebook/1.0/getting-started/meet-strata/new-file-structure/
next_page_label: A New File Structure
---

Strata executes itself parallel to Wordpress during the `init` action.

Should a matching route have been defined, the current URL context will dictate which [Controller](/guidebook/1.0/digging-in/controllers/) is to be instantiated. From this object-oriented entry point a series of actions will then be performed.

During that process you are expected to prepare view variables by running queries on [Models](/guidebook/1.0/digging-in/models/), to prepare [View Helpers](/guidebook/1.0/digging-in/view-helpers/) or handle special [Requests](/guidebook/1.0/handing-requests/).

At the end of this process Wordpress is only aware that new variables have been defined for its template stack and otherwise remains unimpacted.

## No weird impacts

Running in this parallel process ensures segregation of the different concepts that will ease debugging. You will not confuse errors causes from how their are manifested:

* **When a URL is not routed properly** : look to Strata's configuration
* **When a core feature behaves weirdly** : look to plugins or the filters in your template
* **When _your application's_ feature does not work** : look to your application's code.

## No unexpected behavior

This approach means that unless you have explicitly configured the application for something to happen in Strata, it will not occur.

Strata does not autoload custom post types or routes if you have not required it to.
