---
layout: guidebook
title: Custom Post Type Models
permalink: /guidebook/1.0/digging-in/models/custom-post-type-models/
covered_tags: models, custom-post-types
menu_group: models
---

## Creating a custom post type model file

You can generate models that will allow database operations in that they map to custom post types. To do so, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

{% highlight bash linenos %}
$ ./strata generate customposttype Song
{% endhighlight %}

It will generate a couple of files for you, including the actual model file and test suites for the generated class. The model will extend `App\Model\AppCustomPostType` and gain access to DB manipulation objects and methods.

{% highlight bash linenos %}
Scaffolding model Song
  ├── [ OK ] src/model/Song.php
  ├── [ OK ] src/model/Entity/SongEntity.php
  └── [ OK ] tests/model/SongTest.php
{% endhighlight %}

## Enabling autoload

By default Strata Custom Post Types objects are not automatically instantiated in Wordpress. To inform Strata it needs to load a new post type you must add the declaration to `config/strata.php` under the `custom-post-types` key.

{% highlight php linenos %}
<?php
$strata = array(

    // ...

    "custom-post-types" => array(
        "Song",
        "Event"
    )

  // ...

);
?>
{% endhighlight %}

## Wrapping other post types

Such explicit declaration allows for distinction between wrapper models and actual dynamic post types.

You could create wrapper classes against post types that have not been created through Strata. For instance, you could map BBPress topics by creating a model similar to the following example. You would gain all the functionality of a Strata Custom Post Type event if you do not declare the post type yourself.

{% highlight php linenos %}
<?php
namespace App\Model;

class ForumPost extends AppCustomPostType {

    public function getWordpressKey()
    {
        return "reply";
    }
}
?>
{% endhighlight %}

## Customizing the CustomPostType Model isntantiation

You can customize the Custom Post Type declaration by supplying the optional `$configuration` public attribute in the Model class. It allows you to customize the configuration array that is internally sent to `register_post_type`.

As long as you follow the [conventions](http://codex.wordpress.org/Function_Reference/register_post_type) your post type will be created using these customized values, filling the missing options with their default counterparts.

The following example illustrates how we allow the `editor` feature and also make the custom post type accessible in the frontend using the `music-page` slug (ex: `yourwebsite.com/music-page/weezer/`).

{% highlight php linenos %}
<?php
namespace App\Model;

class Artist extends AppCustomPostType {

    public $configuration = array(
        "supports"  => array( 'title', 'editor' ),
        'publicly_queryable' => true,
        "rewrite"   => array(
            'slug' => 'music-page'
        )
    );

}
?>
{% endhighlight %}

## On automated configuration

The custom post type key is generated from the model's class name. By default, this value will be prefixed by `cpt_`. In this example the unique key of the custom post type will be `cpt_artist`.

At all times, you can get the Wordpress key of the model using `wordpressKey()` statically or `getWordpressKey()` from a instance of the object.

{% highlight php linenos %}
<?php
$model = new App\Model\Fruit();
echo $model->getWordpressKey();

$data = new WP_Query(array(
    'post_type'     => App\Model\Profile::wordpressKey()
));
?>
{% endhighlight %}
