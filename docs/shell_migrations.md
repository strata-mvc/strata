---
layout: docs
title: Migrations
permalink: /docs/shell/migrations/
---

Strata does not ship with complex database migration tools but it does offer a simpler in line database import and export tool.

## Exporting SQL

To generate a Migration, you should use the automated generator provided by Strata. It will ensure it will be correctly defined.

Using the command line, run the `db export` from your project's base directory. 3 actions may be supplied to the command

~~~ sh
$ bin/strata db export
~~~

It will generate a `.sql` export of the current state of the database, named with the time is has been generated. You could check in these files to your code repository if you are aware of the implications.

~~~ sh
Generating MySQL export dump to ./db/export_04-13-2015_0628pm.sql
~~~

## Migrating SQL

There are two ways to apply a migration. You either pass in the filename of the migration you wish to load or leave the second parameter empty. When no file is supplied, the most recent .sql file in `db/` will be imported.

~~~ sh
$ bin/strata migrate

# or

$ bin/strata migrate -f db/export_04-13-2015_0628pm.sql
~~~

## Importing from another environment

{% include workinprogress.html %}

Eventually, you'll be able to import sql from other environments.

## MAMP Configuration

You may get a `sh: mysql: command not found` error if you use MAMP (or any similar platform) and have not made the MySQL binary globally accessible.

You can do so by adding it to your export string.

~~~
$ export PATH=$PATH:/Applications/MAMP/Library/bin/
~~~
