---
layout: guidebook
title: Custom Post Type Queries
permalink: /guidebook/1.0/digging-in/models/custom-post-type-queries/
covered_tags: models, custom-post-types, queries
menu_group: models
---

Strata Models generate `Query` adapters that hold configuration data and can be chained or manipulated before triggering the query lookup.

Up to the moment when `fetch()` is called, you can manipulate the query parameters at no cost.

It offers some of the advantages of a full-fledged ORM without bastardizing Wordpress's `WP_Query`.

The following example shows how to query published posts ordered by the menu order.

{% highlight php linenos %}
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
{% endhighlight %}

You can call this query from your controllers by accessing the repository using `repo()`:

{% highlight php linenos %}
<?php
    debug(Artist::repo()->findPublished());
?>
{% endhighlight %}

## $this->where($field, $value);

Querying a Custom Post Type using `where()` will eventually bubble up to Wordpress `[WP_Query](https://developer.wordpress.org/reference/classes/wp_query/)` object.

Each `$field` is added to an internal array that is then sent as first parameter of `WP_Query` when a call to a fetch method ends the chain.

Each of the allowed values that can be sent as `$field` are the same as those documented in Wordpress codex.


## Conventions

There are conventions when building your model's queries. Following them will make your code easier to understand.

* Functions prefixed with `find` will execute a database lookup
* All other querying functions must be chainable
* Functions are not expected to be static as to not break inheritance
* Queries made within Strata are expected to return ModelEntities
* ModelEntities are expected to work transparently as a WP_Post or whichever default Wordpress object class.
