---
layout: docs
title: Controllers
permalink: /docs/controllers/
---

## On controllers

Controllers allow an easy to undertand entry point in moderns web application. Once you have set up the [application's routes](/docs/routes/) in `app.php` you will have to code the corresponding controllers.


## Writing a controllers declaration

Here's how you could declare a controller for the school entity:

~~~ php
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
~~~

You must ensure the class extends `MVC\Controller` which does all the heavy lifting for you. Functions that have routes mapped to them need to be publicly accessible. You can declare variables to send to the templating files and well as instanciate helpers. Note that no helpers or models are auto loaded by controller.

## Shortcodes and exposing actions.

In the case where you create a page in Wordpress and need to include logic in the view, you must use shortcodes.

In the example earlier a shortcode named `my_short_code` is generated and will point to `SchoolController::getCustomAction()`. This means that in Wordpress' WYSIWYG, entering `[my_short_code]` in the post body will print out whatever `SchoolController::getCustomAction()` will return.

Calling shortcodes like these is also usefull when using the [FormHelper](/docs/helpers/formhelper/).

## Setting view variables

To expose a view variable, simple use the controllers `set($key, $mixed)` function. This exposes the value to the templating engine so you can use them in wordpress template files.

In the controller :

~~~ php
    $this->set("school", $myschool);
~~~

In a template file :

~~~ php
    &gt;?php if (isset($school)) : ?>
        echo $school->post_title;
    &gt;?php endif; ?>
~~~


## Before and after

Upon each succesful route match the router will call the `before()` and `after()` functions. These functions can be usefull when setting up objects :

~~~ php
namespace Mywebsite\Controllers;

use MVC\Controller;

class SchoolController extends Controller {

    protected $_repository = null

    public function before()
    {
        if (\MVC\Mvc::config('useGithub')) {
            $this->_repository = "http://github.com/";
        }
    }
}
~~~

## On ajax

Ajax in wordpress can be difficult and we tailored a way to specify the content type and closing the request cleanly.

Assuming we have this routing rule in `app.php`:

~~~ php
array('POST',       '/wp-admin/admin-ajax.php', 'AjaxController#index'),
~~~

Notice here the mapping towards `index()` instead of an action. This is because Wordpress uses `$_POST['action']` to fork ajax requests. Our controller object has a default `index` action that check for this post value should the current request be made through Ajax.

The controller file could look like :

~~~ php
namespace Mywebsite\Controllers;

use MVC\Controller;
use Mywebsite\Models\School;

class AjaxController extends Controller {

    public function schools()
    {
        $this->makeSecure();

        $data = array();
        foreach (School::findByRegionId($_POST['region']) as $school) {
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
~~~

By calling `render()` the current php process will end and we will prevent the rendering of the full wordpress template stack which we don't need as we're printing json data. Valid content-types are those accepted by php `header()` function.

The `makeSecure()` function ensures the ajax request is safe by using `check_ajax_referer` internally.

## On scopes

Because the MVC is trigger by a wordpress event, you may run into cases were the controller's view variables are no longer instanciated. It is the case with shortcode assignments. This is simply because Wordpress' initiation event and the one compiling shortcodes are in different scopes. The reference to the current controller is retained but the view vars cannot be automatically declared. You can however fetch them using `$this->viewVars['varname']`.

