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
namespace App\Model;

use WP_Query;

class Artist extends AppCustomPostType {

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

Every view and template files would then call the centralized `App\Model\Artist::findPublished()` ensuring the query is always correct, testable and unique. Note however, the internal `Query` class is a preferred method of handling `WP_Query` assignments.

## Preferred Query class

While the previous example is perfectly functional, we offer a way to improve on it. Model entities in Strata generate `Query` adapters that hold configuration data and can be chained or manipulated before triggering the query lookup.

Up to the moment when `fetch()` is called, you can manipulate the query parameters at no cost.

It offers some of the advantages of a full-fledged ORM without bastardizing Wordpress's `WP_Query`.

The following example shows how to query published posts ordered by the menu order.

~~~ php
<?php
// src/Model/Artist.php
namespace App;

class Artist extends AppCustomPostType {

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

You can call this query from your controllers by accessing the repository using `repo()`:

~~~ php
<?php
    debug(Artist::repo()->findPublished());
?>
~~~

## Conventions

There are conventions when building your model's queries. Following them will make your code easier to understand.

* Functions prefixed with `find` will execute a database lookup
* All other querying functions must be chainable
* Functions are not expected to be static as to not break inheritance
