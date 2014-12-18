---
layout: docs
title: Controllers
permalink: /docs/controllers/
---

Controllers allow an easy to understand entry point in moderns web applications. Once you have set up the [application's routes]({{ site.baseurl }}/docs/routes/) in `app.php` you will have to write the corresponding controller endpoints.

## Declaring a controller

You must ensure the class extends `MVC\Controller` which does most of the heavy lifting for you. Functions that have routes mapped to them need to be publicly accessible.

In these functions, you can setup variables set to be sent to the templating files as well as instanciate any custom view helpers using `$this->set("varname", $mixedValue)`. Note that no helpers or models are being auto-loaded by controller.

Here's how you could declare a controller for the school entity:

~~~ php
<?php
namespace Mywebsite\Controllers;

use MVC\Controller;

class SchoolController extends Controller {

    public $shortcodes = array("my_short_code" => 'getCustomAction');

    public function view($schoolId = null)
    {
        // custom processes that would instanciate the $myschool variable.

        $this->set("school", $myschool);
    }

    public function getCustomAction()
    {
        $list = \Mywebsite\Model\School::listing();
        return "<p>" . implode(", ", $list) . "</p>";
    }
}
?>
~~~


## Shortcodes and exposing actions.

In the case where you have created a page in Wordpress and need to include dynamic values inside the view's template, you must use shortcodes.

In the example earlier, a shortcode named `my_short_code` is generated and will point to `SchoolController::getCustomAction()`. This means that in Wordpress' WYSIWYG, entering `[my_short_code]` in the post body will print out whatever is returned by `SchoolController::getCustomAction()`. Note that the permalink of this page must be caught by a route in order for the controller to be instanciated and the shortcode to be applied.

Calling shortcodes like these is also useful when using the [FormHelper]({{ site.baserl }}/docs/helpers/formhelper/).

## Setting view variables

To expose a view variable, simple use the controllers `set($key, $mixed)` function. This exposes the value to the templating engine so you can use them in wordpress template files.

In the controller :

~~~ php
<?php
    $this->set("school", $myschool);
?>
~~~

In a template file :

~~~ php
<php if (isset($school)) : ?>
    <p><?php echo $school->post_title; ?></p>
<php endif; ?>
~~~


## Before and after

Upon each succesful route match the router will call the `before()` and `after()` functions. These functions can be usefull when setting up objects :

~~~ php
<?php
namespace Mywebsite\Controllers;

use MVC\Controller;

class SchoolController extends Controller {

    protected $_repository = null

    public function before()
    {
        if ((bool)\MVC\Mvc::config('useGithub')) {
            $this->_repository = "http://github.com/";
        }
    }
}
?>
~~~

## On ajax

Ajax in wordpress can be difficult to achieve and we tailored a way to help. In our controllers, you can specify the content type and trigger the closing of the request cleanly.

Assuming we have this routing rule in `app.php`:

~~~ php
array('POST',       '/wp-admin/admin-ajax.php', 'AjaxController#index'),
~~~

Notice here the mapping towards `index()` instead of an explict action. This is because Wordpress uses `$_POST['action']` to fork ajax requests an not distinct urls.

Our parent root controller object has a default `index` action that checks for this `$_POST` value should the current request have been made through Ajax.

The controller file could look like :

~~~ php
<?php
namespace Mywebsite\Controllers;

use MVC\Controller;
use Mywebsite\Models\School;

class AjaxController extends Controller {

    public function schools()
    {
        $this->makeSecure();

        $data = array();
        foreach (School::findByRegionId((int)$_POST['region']) as $school) {
            $data[] = array(
                "value" => $school->ID,
                "label" => $school->post_title
            );
        }

        $this->render(array(
            "Content-type" => "text/javascript",
            "content" => $data
        ));
    }
}
?>
~~~

By calling `render()` the current php process will end and it will prevent the rendering of the full Wordpress template stack which we don't need as we're printing json data. Valid content-types are those accepted by PHP's `header()` function.

The `makeSecure()` function ensures the ajax request is safe by using `check_ajax_referer` internally.

## On scopes

Because the MVC is triggered by a Wordpress event, you may run into cases were the controller's view variables are no longer instanciated. It is the case with shortcode assignments. This is simply because Wordpress' initiation event and the one compiling shortcodes are in different scopes. The reference to the current controller is retained but the view vars cannot be automatically declared. You can however fetch them using `$this->viewVars['varname']`.

