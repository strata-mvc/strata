---
layout: docs
title: Model Queries
permalink: /docs/models/queries/
---

The main use case for models in Strata is to contain all the database queries used through your application in the same file.

## Custom Queries


You could therefore do the following to contain all queries against a custom post type :

~~~ php
<?php
namespace MyProject\Model;

class Artist extends \Strata\Model\CustomPostType\Entity {

    public static function findPublished()
    {
        $config = array(
            'post_type'     => self::wordpressKey(),
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $data = new \WP_Query($config);

        return $data->posts;
    }
}
?>
~~~

Every view and template files would then call the centralized `MyProject\Model\Artist::findPublished()` ensuring the query is always correct, testable and unique. Note however, the internal Query class is a preferred method of handling `WP_Query` assignments.

## Internal Query class

While the previous example is perfectly functional, we offer a way to improve on it. Model entities in Strata generate `Query` objects that will hold configuration data that can be chained and manipulated before triggering the query. Up to the moment when `fetch()` is called, you can manipulate the query parameters.

It offers some of the advantages of a full-fledged ORM without bastardizing Wordpress's `WP_Query`.

The following example shows how to query published posts ordered by the menu order.

~~~ php
<?php
namespace MyProject\Model;

class Artist extends \Strata\Model\CustomPostType\Entity {

    public static function findPublished()
    {
        return self::query()->status("published")->where("orderby", "menu_order")->fetch();
    }
}
?>
~~~

If you create your own instance of the `Query` class, you can start chaining your data based on concepts from your business logic.

~~~ php
<?php
namespace MyProject\Model;

class ArtistQuery extends \Strata\Model\CustomPostType\Query {

    public function published()
    {
        $this->where('post_status', "published");
        return $this;
    }

    public function consideringMetaKey()
    {
        $this->where('meta_key', "something");
        $this->where('meta_value', "value");
        return $this;
    }

    public function publishedWithMeta()
    {
        return $this->published()->consideringMetaKey();
    }
}
?>
~~~

Afterwards, override your model's `query()` function to allow it to use your custom query object and customize your query so it better suits your needs.

~~~ php
<?php
namespace MyProject\Model;

use MyProject\Model\ArtistQuery;

class Artist extends \Strata\Model\CustomPostType\Entity {

    public static function query()
    {
        $query = new ArtistQuery();
        // Set the post type of the query to the current custom post type.
        // Not that the constructor does not returns a chainable reference.
        return $query->type(self::wordpressKey());
    }

    public static function findPublished()
    {
        return self::query()->publishedWithMeta()->fetch();
    }
}
?>
~~~
