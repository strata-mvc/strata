---
layout: guidebook
title: Adding A Theme
permalink: /guidebook/1.0/getting-started/creating-projects/adding-a-theme/
covered_tags: theme
menu_group: creating-projects
---

Strata does not require a special theme and you may install whichever you like. Note that themes are recommended to remain simple in functionality as to not duplicate behavior provided by Strata unnecessarily. Additionally, themes should strictly follow Wordpress standard because in a Strata environments the different file locations do change and cannot be hard coded.

Simply copy or checkout your theme's code under `~/web/app/themes/` and activate it from the Wordpress administration area.

To illustrate the process, the following is the command required to install [Sage](https://roots.io/sage/), a good theme for Strata. Checkout the source from the root of your project and activate the theme form the Wordpress administration afterwards:

{% include terminal_start.html %}
{% highlight bash %}
$ git clone https://github.com/roots/sage.git web/app/theme/your-custom-theme-name
{% endhighlight %}
{% include terminal_end.html %}
