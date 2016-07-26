---
layout: guidebook
title: Automatic Shortcodes
permalink: /guidebook/1.0/digging-in/controllers/automatic-shortcodes/
covered_tags: controller, shortcodes
menu_group: controllers
---

In the case where you have created a page through Wordpress's CMS and need to include dynamic values inside the post's content you may use automatically generated shortcodes.

In this example a shortcode named `list_songs` is registered by Strata and will point to `SongController::getSongsListing()`. This means that in Wordpress' WYSIWYG, entering `[list_songs]` in the post body will print out whatever is returned by `SongController::getSongListing()`.


{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Controller;

use App\Model\Song;

class SongController extends AppController {

    public $shortcodes = array(
        "list_songs" => "getSongListing"
    );

    public function getSongListing()
    {
        $list = Song::repo()->onlyGlamRock()->listing("ID", "post_title");
        return "<p>" . implode(", ", $list) . "</p>";
    }
}
{% endhighlight %}
{% include terminal_end.html %}

Note that the permalink of this page must be caught by a route in order for the Controller object to be instantiated and have its shortcodes be declared and applied.

In other words you wouldn't have access to this shortcode if another controller was called or if the request did not match any predefined routes.

Shortcodes that need to be applied in multiple areas much be declared the regular Wordpress way to make it aware of the shortcode, but can still be routed to a Controller using [Router::callback()](/guidebook/1.0/digging-in/routing/callback-routing/).
