---
layout: guidebook
title: Middlewares
permalink: /guidebook/1.0/digging-in/middlewares/
entry_point: true
menu_group: middlewares
group_label: Middlewares
group_theme: Digging In
---

Middlewares in Strata are used to handle concepts that may have an impact on the application but are not considered mandatory for the project core concepts.

For instance IP Forwarding, URL prefixing and various external API setups could be considered good Middlewares because they do not directly impact the internal business logic of the application.

In comparison configuring a Model that connects to a web service is not considered Middleware becuase it does not configure the application behavior. It's actual behavior.

A Middleware class should only contain the basic instructions is needs to kickoff it's mandate. A Middlewares will therefore be an initializer for another object that contains the necessary methods for the actual heavy lifting.

In that sense Middlewares should only initialize and configure other classes and prepare them for execution. That is why Middlewares are expected to have the suffix "Initializer" and have a public method named `initialize()`. Strata will automatically load and attempt to run any PHP file matching the pattern under `~/src/Middleware/`.

You may call Wordpress functions (like `add_filter` and `add_action`) to integrate the object into the application, but Wordpress is not completely booted when Middlewares are ran and `$wp_query` is not yet available.
