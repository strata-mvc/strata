---
layout: docs
title: Controllers
permalink: /docs/controllers/
---

Controllers allow an easy to understand entry point in modern web applications. Once you have set up the [application's routes]({{ site.baseurl }}/docs/routes/) in your project's `app.php` you will have to write the corresponding controller endpoints.

## Creating a controller file

To generate a Controller, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a controller for the `Artist` object:

~~~ sh
$ bin/mvc generate controller Artist
~~~

It will generate a couple of files for you, including the actual controller file and test suites for the generated class.

~~~ sh
Scaffolding controller Artist
src/controller/ArtistController.php
tests/controller/ArtistController.php
~~~

The main use case of Controllers in Strata is to replace the need of placing pure code in the top area of a template file. Instead of instantiating queries and various variables inside a template file, you should place the code in a controller. This biggest advantage, apart from code clarity, is that you gain the ability to use the same business logic code in multiple themes as well as improving code testability.

Controllers may seem like overkill if you only use the regular templating. You can achieve similar results by placing the code in a custom `template-customposttype.php` file. It's up to you to decide if you need such infrastructure in your project.

 On the other hand, when handling posted data on a Wordpress CMS page or for any other reasons when the page can have a dynamic state, controllers become a great way of separating your code.

Note that no helpers or models are being auto-loaded by the controller.

Here's how you could declare a controller for a Song entity:

~~~ php
<?php
namespace Mywebsite\Controller;

use Mywebsite\Model\Song;

class SongsController extends \MyProject\Controller\AppController {

    public $shortcodes = array("list_songs" => 'getSongsListingShortcode');

    public function index()
    {
    }

    public function view($songId = null)
    {
        if (!is_null($songId)) {
            $this->set("song", Song::findById((int)$songId));
        }
    }

    public function getSongsListingShortcode()
    {
        $list = Song::query()->listing("ID", "post_title");
        return "<p>" . implode(", ", $list) . "</p>";
    }
}
?>
~~~

## Shortcodes and exposing actions.

In the case where you have created a page in Wordpress and need to include dynamic values inside the post's CMS content, you may use auto-generated shortcodes.

In the earlier example, a shortcode named `list_songs` is generated and will point to `SongController::getSongsListingShortcode()`. This means that in Wordpress' WYSIWYG, entering `[list_songs]` in the post body will print out whatever is returned by `SongController::getSongsListingShortcode()`.

Note that the permalink of this page must be caught by a route in order for the controller to be instantiated and the shortcode to be declared and applied.

In other words, you wouldn't have access to this shortcode if another controller was called (or if the request did not match any controller). Shortcodes that need to be applied in multiple areas much be declared the regular Wordpress way to make it aware of the shortcode, but can be routed to a controller using [\Strata\Router::callback()]({{ site.baserl }}/docs/routes/).

Generating shortcodes like these is useful when using the [FormHelper]({{ site.baserl }}/docs/helpers/formhelper/) or when generating data that needs to be manipulated right form CMS data.

## Setting view variables

To expose a variable to the regular Wordpress templating, use the controllers' `set($key, $mixed)` method. This exposes the value to the templating engine so you can use them in Wordpress' template files.

In the controller :

~~~ php
<?php
    $this->set("song", $mysong);
?>
~~~

In a template file :

~~~ php
<php if (isset($song)) : ?>
    <p><?php echo $song->post_title; ?></p>
<php endif; ?>
~~~


## Before and after

Upon each successful route match the router will call the `before()` and `after()` functions. These functions can be useful when setting up objects or adding validation :

~~~ php
<?php
namespace Mywebsite\Controller;

use MVC\Controller;

class AdminController extends Controller {

    protected $_repository = null

    public function before()
    {
        if ((bool)\Strata\Mvc::config('useGithub')) {
            $this->_repository = "http://github.com/";
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

class AjaxController extends \MyProject\Controller\AppController {

    public function songs()
    {
        $this->makeSecure();

        $data = Song::query()
            ->where("meta_key", "album_name")
            ->where("meta_value", $_POST['album_name'])
            ->listing("ID", "post_title");

        $this->render(array(
            "Content-type" => "text/javascript",
            "content" => $data
        ));
    }
}
?>
~~~

The `makeSecure()` function ensures the ajax request is safe by using `check_ajax_referer` internally.

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

## On file downloads

By default, calling `render()` will end the current php process and it will prevent the rendering of the full Wordpress template stack (which we don't need as we are printing partial data). This function allows you to specify the content-type of the returned value as well as additional PHP's `header()` values. It is easy to set up file downloads by entering known PHP header arguments:

~~~ php
<?php
namespace Mywebsite\Controller;

use Mywebsite\Model\Form\CSVExportForm;

class FileController extends \MyProject\Controller\AppController {

    public function downloadcvs()
    {
        $form = new CSVExportForm();
        $this->render(array(
            "Content-type"          => 'text/csv',
            "Content-disposition"   => "attachment;filename=" . $form->getCSVFilename(),
            "content"               => $form->filterResultsToCSV()
        ));
    }
}
?>
~~~


## On scopes and custom templating

Because the MVC is triggered by a Wordpress event, you may run into cases were the controller's view variables are no longer instantiated. It is the case with shortcode assignments and some callbacks. This is because Wordpress' initiation event and the ones executing the shortcodes are in different PHP scopes.

The reference to the current controller is retained but the view vars cannot be automatically declared. You can however fetch them using `$this->viewVars['varname']`.

Say you create a dashboard widget:

~~~ php
<?php
    wp_add_dashboard_widget('mywebsite-test-dashboard', 'This is a test dashboard',  \Strata\Router::callback('AdminController', 'testDashboardMetabox'));
?>
~~~

The metabox uses its own template file, outside of the current page rendering. You must send the controller's view variables manually to retain them on the new template file. Allow the rendering process to complete using the `end` argument.

~~~ php
<?php
namespace Mywebsite\Controller;

class AdminController extends \MyProject\Controller\AppController {

    public function before()
    {
        $this->set("testvar3", "before!");
    }

    public function testDashboardMetabox()
    {
        $this->set("testvar1", 1);
        $this->set("testvar2", "abc");

        $this->render(array(
            "content" => Controller::loadTemplate('admin'.DIRECTORY_SEPARATOR.'dashboard'.DIRECTORY_SEPARATOR.'testDashboard', $this->viewVars),
            "end" => false
        ));
    }
}
?>
~~~

Finally, your template file located at `[website_theme]/templates/admin/dashboard/testDashboard.php` would look like the following, even though that template was not the one dynamically parsed by Wordpress:

~~~ php
<ul>
    <li>Testvar 1 : <?php echo $testvar1; ?></li>
    <li>Testvar 2 : <?php echo $testvar2; ?></li>
    <li>Testvar 3 : <?php echo $testvar3; ?></li>
</ul>
~~~
