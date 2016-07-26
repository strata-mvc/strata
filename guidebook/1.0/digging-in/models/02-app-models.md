---
layout: guidebook
title: AppModel
permalink: /guidebook/1.0/digging-in/models/app-models/
covered_tags: models, app-model
menu_group: models
---

## Creating a model file

To generate a Model in which you can add business logic but cannot CRUD entities to Wordpress' database, you should use the automated generator provided by Strata. It will validate your object's name and ensure it is defined following the intended conventions.

Look at [automated custom post type models](/guidebook/1.0/digging-in/models/custom-post-types/) for information on how to create models that map to database entries.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a model named `Artist` :

{% include terminal_start.html %}
{% highlight bash linenos %}
$ ./strata generate model artist
{% endhighlight %}
{% include terminal_end.html %}

It will generate a couple of files for you, including the actual model file and test suites for the generated class.

{% include terminal_start.html %}
{% highlight bash linenos %}
Scaffolding model Artist
  ├── [ OK ] src/Model/Artist.php
  ├── [ OK ] src/Model/Entity/ArtistEntity.php
  └── [ OK ] test/Model/ArtistTest.php
  ├── [ OK ] test/Model/Entity/ArtistEntityTest.php
{% endhighlight %}
{% include terminal_end.html %}

## Building a data supplier

The idea behind `AppModels` not associated to a Wordpress custom post type is to have a single unique place where you can store information and behavior in a meaningful way. For instance, the `Artist` model can supply a list of genre's throughout the application.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model;

class Artist extends AppModel
{
    public static function listGenres()
    {
        return array(
            "Classical",
            "Pop",
            "Rock",
        );
    }
}
{% endhighlight %}
{% include terminal_end.html %}

A `Model` is the perfect type of object unto which add API access to a remote services.
