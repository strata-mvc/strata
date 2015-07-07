---
layout: docs
title: Controllers
permalink: /docs/controllers/
---

Controllers allow easy to understand entry points in modern web applications. Once you have set up at least one [application route]({{ site.baseurl }}/docs/routes/) in your project's [configuration file]({{ site.baseurl }}/docs/configuring/#strata-configuration) you will have to write the corresponding controller endpoints.

The main use case of Controllers in Strata is to replace the need of placing pure code in template file. Instead of instantiating queries and various variables inside the template file, you should place the code in a controller. The biggest gain is the ability to use the same business logic code multiple times as well as improving code testability.

## Creating a controller file

To generate a Controller, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined following the guidelines.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a controller for the `Artist` object:

~~~ bash
$ bin/strata generate controller Artist
~~~

It will generate a couple of files for you, including the actual controller file and test suites for the generated class.

~~~ sh
Scaffolding controller ArtistController
  ├── [ OK ] src/controller/ArtistController.php
  └── [ OK ] test/controller/ArtistControllerTest.php
~~~

# Example class

Here's how you could declare a controller for a Song entity:

~~~ php
<?php
namespace Mywebsite\Controller;

use Mywebsite\Model\Song;

class SongsController extends \Mywebsite\Controller\AppController {

    public $shortcodes = array("list_songs" => 'getSongsListing');

    public function index()
    {
    }

    public function show($songId = null)
    {
        if (!is_null($songId)) {
            $this->view->set("song", Song::repo()->findById((int)$songId));
        }
    }

    public function getSongsListing()
    {
        $list = Song::repo()->inGlamRock()->listing("ID", "post_title");
        return "<p>" . implode(", ", $list) . "</p>";
    }
}
?>
~~~

## Shortcodes and exposing actions

In the case where you have created a page in Wordpress and need to include dynamic values inside the post's CMS content, you may use auto-generated shortcodes.

In the earlier example, a shortcode named `list_songs` is generated and will point to `SongController::getSongsListing()`. This means that in Wordpress' WYSIWYG, entering `[list_songs]` in the post body will print out whatever is returned by `SongController::getSongsListing()`.

Note that the permalink of this page must be caught by a route in order for the controller to be instantiated and the shortcode to be declared and applied.

In other words, you wouldn't have access to this shortcode if another controller was called (or if the request did not match any controller). Shortcodes that need to be applied in multiple areas much be declared the regular Wordpress way to make it aware of the shortcode, but can be routed to a controller using [\Strata\Router::callback()]({{ site.baserl }}/docs/routes/).

Generating shortcodes like these is useful when using the [FormHelper]({{ site.baserl }}/docs/helpers/formhelper/) or when generating data that needs to be manipulated right form CMS data.


## Before and after

Upon each successful route match the router will call the `before()` and `after()` functions. These functions can be useful when setting up objects or adding validation :

~~~ php
<?php
namespace Mywebsite\Controller;

class AdminController extends \Mywebsite\Controller\AppController {

    protected $_repository = null

    public function before()
    {
        if ((bool)\Strata\Strata::config('useGithub')) {
            $this->_repository = "http://github.com/xyz/";
        } else {
            $this->_repository = "http://bitbucket.org/xyz/";
        }

        if (!is_admin()) {
            throw new \Exception("This controller is expected to map to the admin.");
        }
    }
}
?>
~~~

## On ajax

Ajax in Wordpress can be difficult to achieve and we tailored a way to help. In our controllers, you can specify the rending method along side a content type and various options to ease request capture.

Assuming we have this routing rule in `app.php`:

~~~ php
array('POST',       '/wp-admin/admin-ajax.php', 'AjaxController'),
~~~

Notice here that no method has been entered as action of the `AjaxController` route. This is because Wordpress uses `$_POST['action']` to fork ajax requests and do not use distinct urls. Therefore the controller will call the method matching the value of the posted `action` parameter.

The controller file could look like :

~~~ php
<?php
namespace Mywebsite\Controller;

use Mywebsite\Model\Song;

class AjaxController extends \Mywebsite\Controller\AppController {

    public function before()
    {
        check_ajax_referer( SECURITY_SALT, 'security' );
    }

    public function songs()
    {
        $data = Song::query()
            ->where("meta_key", "album_name")
            ->where("meta_value", $_POST['album_name'])
            ->listing("ID", "post_title");

        $this->view->render(array(
            "Content-type" => "text/javascript",
            "content" => $data
        ));
    }
}
?>
~~~

And the javascript call could be :

~~~ js
<script>
    $.ajax({
        url: <?php echo admin_url('admin-ajax.php'); ?>,
        method: 'POST',
        data: {
            action: 'songs',
            security: wp_create_nonce(SECURITY_SALT),
            album_name: $('input[name=album_name]').val()
        }
    }).done(function(data){
       console.log(data);
    });
</script>
~~~
