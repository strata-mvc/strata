---
layout: docs
title: Admin menus on custom post types
permalink: /docs/models/adminmenus/
---

By default, creating a model will add it to the list of custom post types in Wordpress' backend with the basic 'All posts', 'Add new' links.

You can add additional admin menu links based on the model when configuring the model entry in `app.php` found in `/lib/wordpress-mvc/` under your theme's directory.

~~~ php
<?php
$app = array(
    "custom-post-types" => array(
        "Profile" => array(
            "admin" => array(
                "exportProfiles" => array("route" => "AdminController", "title" => "Export", "menu-title" => "Export")
                "secondProfileAction" => array("route" => "AdminController", "title" => "Additional link", "menu-title" => "Additional link")
            )
        ),
        "Song" => array(
            "admin" => array(
                "extraSongInfo" => array("title" => "Extra song information", "menu-title" => "Extra song information")
            )
        ),
    )
);
?>
~~~

In the previous example, two model entities will be automatically set up at runtime, `Profile` and `Song`. By passing the `admin` parameter, you can have the model generate a call to `add_submenu_page` based on the information passed.

Each key of the `admin` configuration array will map to a controller's method. If the `route` parameter is present, it will be forwarded to this controller. If the parameter is not present, it will be forwarded to the model's controller.

In the example above, `Profile` will have two links added. The first will link to the `exportProfiles` of the `AdminController` while the second will link to `secondProfileAction` also of the `AdminController` class.

On the other hand, `Song` will have only added one link. It will attempt to call `extraSongInfo` of the `SongController` class.

<p class="warning">
    Make sure you understand how pages are <a href="/docs/controllers/view/#on-rendering-in-the-admin">being rendered in the Admin area</a> to obtain the correct behavior.
</p>
