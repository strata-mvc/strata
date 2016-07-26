---
layout: guidebook
title: Raw SQL in Models
permalink: /guidebook/1.0/digging-in/models/raw-sql-in-models/
covered_tags: models, custom-post-types, queries, sql
menu_group: models
---

You can bundle all of the related queries in the same object. It will make it much easier to create a central area that contains all queries against a custom post type :

{% include terminal_start.html %}
{% highlight php linenos %}
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
{% endhighlight %}
{% include terminal_end.html %}

Every view and template files would then call the centralized `App\Model\Artist::findPublished()` ensuring the query is always correct, testable and unique. Note however, the internal `Query` class is a preferred method of handling `WP_Query` assignments as it is a more descriptive process.
