---
layout: guidebook
title: URL Rewriting
permalink: /guidebook/1.0/digging-in/models/url-rewriting/
covered_tags: models, custom-post-types, url-rewrite
menu_group: models
---

There may be cases when you need additional sub-URLs for your custom post type. For instance you can have a tab view widget on a custom post type's `single-*.php` template upon which you want to track each tabs with a unique URLs.

For two tabs you would then need your application to support the following URLs :

* `mydomain.com/my-custom-post-type/tab-1`
* `mydomain.com/my-custom-post-type/tab-2`

Strata can add these rewrite rules based on the Custom Post Type's configuration and automatically route them to a Controller.

The following configuration would achieve this. Note the notation where the array key is a unique identifier while the second parameter corresponds to the actual URL slug.

{% highlight php linenos %}
<?php
namespace App\Model;

class MyCPT extends AppCustomPostType
{
     public $routed = array(
        "rewrite" =>  array(
            'tab_1' => 'tab-1',
            'tab_2' => 'tab-2',
        ),
    );
}
?>
{% endhighlight %}
