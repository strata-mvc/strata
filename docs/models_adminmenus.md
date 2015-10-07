---
layout: docs
title: Admin menus on custom post types
permalink: /docs/models/adminmenus/
---

Registering a model will add it to the list of custom post types in Wordpress' admin sidebar with the basic 'All posts' and 'Add new' links.

During the registering process, Strata will look for `admin_menus` on the instantiated model and will automatically configure the setup of additional menu link under the custom post type in the backend.

You can add additional menu links to the post type when configuring the model entry in  like so:

Explicitly declare autoloaded models in `config/strata.php`:
~~~ php
<?php
$strata = array(
    "custom-post-types" => array(
        "Profile",
        "Song",
    )
);

return $strata;
?>
~~~

Add the admin menus from within the model class. The definition for Profile inside of `src/Model/Profile.php` would look like :

~~~ php
<?php
namespace App\Model;

class Profile extends AppCustomPostType {

    public $admin_menus = array(
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
    );

    public $configuration = array(
        "supports"  => array('title', 'editor'),
        'publicly_queryable' => true,
        "rewrite"   => array(
            'slug'                => 'our-company/profiles',
            'with_front'          => false,
        ),
        "menu_icon" => "dashicons-businessman"
    );
}
?>
~~~

While the definition for Song within `src/Model/Song.php` could look like :

~~~ php
<?php
namespace App\Model;

class Song extends AppCustomPostType {

    public $admin_menus =  array(
        "extraSongInfo" => array(
            "title" => "Extra song information",
            "menu-title" => "Extra song information"
        )
    );

    public $configuration = array(
        "supports"  => array('title', 'editor', 'thumbnail'),
        "menu_icon" => "dashicons-businessman"
    );
}
?>
~~~

In the previous example, two model entities will be automatically set up at runtime: `Profile` and `Song`. By passing the `admin_menus` attribute you will see the model generate a call to `add_submenu_page` based on the information passed.

Each key of the `admin_menus` attribute array will map to a controller's method. If the `route` parameter is present, it will be forwarded to this controller. If the parameter is not present, it will be forwarded to the model's implied controller.

In the example above, `Profile` will have two links added. The first will link to `AdminController#exportProfiles()` while the second will link to `AdminController#secondProfileAction()`.

On the other hand, `Song` will only have added one link. It will attempt to call `SongController#extraSongInfo()`.

<p class="warning">
    Make sure you understand how pages are <a href="/docs/controllers/view/#on-rendering-in-the-admin">being rendered in the Admin area</a> to ensure you are obtaining the correct behavior once you have hooked into the backend.
</p>
