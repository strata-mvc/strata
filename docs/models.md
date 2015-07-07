---
layout: docs
title: Models
permalink: /docs/models/
---

## Model entities, tables and Custom Post Types

Models are where the logic being the website's custom processes, validations and everything that is generally defined as "business logic" is located.

## Creating a model file

To generate a flat Model in which you can add business logic but cannot save entities to Wordpress' database, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

Look at [automated custom post type models](/docs/models/customposttypes/) for information on how to create models that map to database entries.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a model named `Artist` :

~~~ sh
$ bin/strata generate model Artist
~~~

It will generate a couple of files for you, including the actual model file and test suites for the generated class.

~~~ sh
Scaffolding model Artist
  ├── [ OK ] src/model/Artist.php
  └── [ OK ] tests/model/ArtistTest.php
~~~


## Insert, Create, Delete

Models extending `\Strata\Model\CustomPostType\Entity` will inherit static functions named `create`, `update` and `delete` that map to `wp_insert_post`, `wp_update_post` and `wp_delete_post`. They supports the same arguments as their Wordpress counterparts.

They exist as entry point for data manipulation but are not intended to replace core Wordpress functions.
