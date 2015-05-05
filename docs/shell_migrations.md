---
layout: docs
title: Migrations
permalink: /docs/shell/migrations/
---

Strata does not ship with a complex database migration tool but it does offer a simpler in line database import and export tool.

{% include workinprogress.html %}

## Dumping SQL

To generate a Migration, you should use the automated generator provided by Strata. It will ensure it will be correctly defined.

Using the command line, run the `db dump` from your project's base directory. 3 actions may be supplied to the command

~~~ sh
$ bin/strata db dump
~~~

It will generate a `.sql` export of the current state of the database, named with the time is has been generated. You could check in these files to your code repository if you are aware of the implications.

~~~ sh
Generating MySQL export dump to ./db/dump_04-13-2015_0628pm.sql
~~~

## Migrating SQL

There are two ways to apply a migration. You either pass in the filename of the migration you wish to load or leave the second parameter empty. When no file is supplied, the most recent .sql file in `db/` will be imported.

~~~ sh
$ bin/strata migrate

# or

$ bin/strata migrate dump_04-13-2015_0628pm.sql
~~~

## Importing from another environment

{% include workinprogress.html %}

Eventually, you'll be able to import sql from other environments.
