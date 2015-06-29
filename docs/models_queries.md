---
layout: docs
title: Model Queries
permalink: /docs/models/queries/
---

Strata models are classes that contain the business logic of your application. It is were operations are handled, done using contained functions that can be inherited and shared.

Another frequent use case for models in Strata is to express database queries in a readable and reusable way through custom post types objects.

## Explicit Wordpress Queries

You can bundle all of the related queries in the same object. It will make it much easier to create a central area that contains all queries against a custom post type :

~~~ php
<?php
namespace MyProject\Model;

use WP_Query;

class Artist extends \Strata\Model\CustomPostType\Entity {

    public static function findPublished()
    {
        $config = array(
            'post_type'     => self::wordpressKey(),
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $data = new WP_Query($config);

        return $data->posts;
    }
}
?>
~~~

Every view and template files would then call the centralized `MyProject\Model\Artist::findPublished()` ensuring the query is always correct, testable and unique. Note however, the internal `Query` class is a preferred method of handling `WP_Query` assignments.

## Internal Query class

While the previous example is perfectly functional, we offer a way to improve on it. Model entities in Strata generate `Query` adapters that hold configuration data and can be chained or manipulated before triggering the query lookup.

Up to the moment when `fetch()` is called, you can manipulate the query parameters at no cost.

It offers some of the advantages of a full-fledged ORM without bastardizing Wordpress's `WP_Query`.

The following example shows how to query published posts ordered by the menu order.

~~~ php
<?php
// src/Model/Artist.php
namespace MyProject\Model;

class Artist extends \Strata\Model\CustomPostType\Entity {

    public function findPublished()
    {
        return $this->published()->byMenuOrder()->fetch();
    }

    public function published()
    {
        return $this->status("published");
    }

    public function byMenuOrder()
    {
        return $this->orderby("menu_order");
    }
}
?>
~~~
