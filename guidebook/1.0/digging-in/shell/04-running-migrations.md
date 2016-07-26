---
layout: guidebook
title: Running Migrations
permalink: /guidebook/1.0/digging-in/shell/running-migrations/
menu_group: shell
---

Strata does not ship with complex database migration tools but it does offer a simpler database import and export tool.

## Exporting SQL

To generate a Migration, you should use the automated generator provided by Strata. It will ensure it will be correctly defined.

Using the command line, run the `db export` from your project's base directory.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata db export
{% endhighlight %}
{% include terminal_end.html %}

It will generate a `.sql` export of the current state of the database, named with the time is has been generated. You could check in these files to your code repository as they are stored outside of the web facing directory.

{% include terminal_start.html %}
{% highlight bash linenos %}
Generating MySQL export dump to ./db/export_04-13-2015_0628pm.sql
{% endhighlight %}
{% include terminal_end.html %}

## Migrating SQL

There are two methods for applying a migration. You either pass in the filename of the migration you wish to load or leave the second parameter empty. When no filename is supplied, the most recent .sql file in `db/` will be imported.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata migrate
{% endhighlight %}
{% include terminal_end.html %}

## or

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata migrate -f db/export_04-13-2015_0628pm.sql
{% endhighlight %}
{% include terminal_end.html %}

## MAMP Configuration

You may get a `sh: mysql: command not found` error if you use MAMP (or any similar platform really) and have not made the MySQL binary globally accessible.

You can do so by adding it to your export string.

{% include terminal_start.html %}
{% highlight bash linenos %}
$ export PATH=$PATH:/Applications/MAMP/Library/bin/
{% endhighlight %}
{% include terminal_end.html %}
