---
layout: guidebook
title: Completing the installation
permalink: /guidebook/1.0/getting-started/creating-projects/completing-the-installation/

covered_tags: installation

menu_group: creating-projects
next_page: /guidebook/1.0/getting-started/creating-projects/launching-a-server/
next_page_label: Launching a server
---

With the database credentials configured you can finally complete the installation. Though at this point all the files are present in the project, the database is still empty.

Invoke the `create` command of the `db` object to complete the installation.

{% highlight bash linenos %}
$ ./strata db create
{% endhighlight %}

This will create the tables used by Wordpress, pre-populate them with default data and create a default administrator user.

At this point, you can start a server and have a look at your successful installation.


