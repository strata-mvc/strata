---
layout: docs
title: Admin menus on custom post types
permalink: /docs/models/adminmenus/
---

Registering a model will add it to the list of custom post types in Wordpress' admin sidebar with the basic 'All posts' and 'Add new' links.

You can add additional menu links to the post type when configuring the model entry in `config/strata.php` like so:

~~~ php
<?php
$strata = array(
    "custom-post-types" => array(
        "Profile" => array(
            "admin" => array(
                "exportProfiles" => array(
                    "route" => "AdminController",
                    "title" => "Export",
                    "menu-title" => "Export"
                ),
                "secondProfileAction" => array(
                    "route" => "AdminController",
                    "title" => "Additional link",
                    "menu-title" => "Additional link"
                )
            )
        ),
        "Song" => array(
            "admin" => array(
                "extraSongInfo" => array(
                    "title" => "Extra song information",
                    "menu-title" => "Extra song information"
                )
            )
        ),
    )
);

return $strata;
?>
~~~

In the previous example, two model entities will be automatically set up at runtime, `Profile` and `Song`. By passing the `admin` parameter, you can have the model generate a call to `add_submenu_page` based on the information passed.

Each key of the `admin` configuration array will map to a controller's method. If the `route` parameter is present, it will be forwarded to this controller. If the parameter is not present, it will be forwarded to the model's implied controller.

In the example above, `Profile` will have two links added. The first will link to `AdminController#exportProfiles()` while the second will link to `AdminController#secondProfileAction()`.

On the other hand, `Song` will only have added one link. It will attempt to call `SongController#extraSongInfo()`.

<p class="warning">
    Make sure you understand how pages are <a href="/docs/controllers/view/#on-rendering-in-the-admin">being rendered in the Admin area</a> to ensure you are obtaining the correct behavior once you have hooked into the backend.
</p>
