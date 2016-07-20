---
layout: guidebook
title: Controller API
permalink: /guidebook/1.0/digging-in/controllers/api/
covered_tags: controller, api
menu_group: controllers
---

The following is the list of available tools provided by the Controller class. More in-depth information can be obtained on it's [detailed API page](/api/classes/Strata_Controller_Controller.html).

## Public attributes

### $this->request

Holds a reference to the request that is currently active in the process. It can be used to see information on the current page state. See [Handling requests](/guidebook/1.0/digging-in/handling-requests/) for additional information.

{% highlight php linenos %}
<?php
namespace App\Controller;

class MyController extends AppController {

    // Inherited
    // public $request;

    public function index()
    {
        $this->request->hasGet("foo");
        $this->request->post("bar");
    }
}
?>
{% endhighlight %}

### $this->view

Holds a reference to the active View object. See [Sending variables to views](/guidebook/1.0/digging-in/controllers/sending-variables-to-views/) for additional information.

{% highlight php linenos %}
<?php
namespace App\Controller;

class MyController extends AppController {

    // Inherited
    // public $view;

    public function index()
    {
        $this->view->check("foo");
        $this->view->get("bar");
        $this->view->set("raynors", "raiders");
        $this->view->loadHelper("Form");
    }
}
?>
{% endhighlight %}


### $this->shortcodes

Allows automatic creation of shortcodes within the scope of the Controller. See [Automatic shortcodes](/guidebook/1.0/digging-in/controllers/automatic-shortcodes) for additional information.

{% highlight php linenos %}
<?php
namespace App\Controller;

use App\Model\ForumPost;

class ForumController extends AppController {

    public $shortcodes = array(
        "app\_post_score"        => "scoringWidget",
    );

    public function scoringWidget()
    {
        $reply = get_post(123);
        $this->view->set("reply", new ForumPost::getEntity($reply));

        return $this->view->loadTemplate("forums/scoreWidget");
    }
}
?>
{% endhighlight %}

### $this->helpers

Allows automatic instantiation of Helpers within the scope of the Controller. See [Helpers](/guidebook/1.0/digging-in/helpers/) for additional information.

{% highlight php linenos %}
<?php
namespace App\Controller;

class AppController extends AppController {

    public $helpers = array(
        "Acf" => array("name" => "Acf"),
        "I18n" => array("name" => "i18n"),
        "Form",
        "Youtube",
        "Gtm"
    );

    public function before()
    {
        $this->view->loadHelper("Category");
    }
}
?>
{% endhighlight %}

## Public methods

### init()

Initiates the Controller object by setting up the Request, the associated View and various autoloaders.

`init()` is invoked each time a route requests a Controller and it is the first method that gets called. While its acceptable to set configurations in this method, it is more elegant to place it in the `before()` method that get called just after in the call stack.

Make sure to call `parent::init();` if you need to plug into it as it would prevent the object from building properly.

### after()

Executed after each calls to a Controller's action. Recommended area for placing cleanup functions.

{% highlight php linenos %}
<?php
namespace App\Controller;

use Strata\Strata;

class AppController extends AppController {

    public function after()
    {
        Strata::log("this is done", "doneLogger");
    }
}
?>
{% endhighlight %}


### before()

Executed before each calls to a Controller's action. Recommended area for initiating objects and running code that is common across a Controller's methods.

{% highlight php linenos %}
<?php
namespace App\Controller;

class EmailController extends AppController {

    public function before()
    {
        $this->view->setConfig("layout", "layout/email-layout");
    }
}
?>
{% endhighlight %}

### before()

Base action when no action is found. This is used mainly as a precautionary fallback when a route matches a controller but not a method.

{% highlight php linenos %}
<?php
namespace App\Controller;

class MyController extends AppController {

    public function noActionMatch()
    {

    }

}
?>
{% endhighlight %}

### notFound()

Applies a valid 404 status to the current Request.

{% highlight php linenos %}
<?php
namespace App\Controller;

class MyController extends AppController {

    public function show($postID)
    {
        $post = get_post($postID);
        if (!$post) {
            return $this->notFound();
        }

    }

}
?>
{% endhighlight %}

### serverError()

Applies a valid 500 status to the current Request.

{% highlight php linenos %}
<?php
namespace App\Controller;

class MyController extends AppController {

    private $featureIsReady = false;

    public function show($postID)
    {
        if (!$this->featureIsReady) {
            return $this->serverError();
        }
    }

}
?>
{% endhighlight %}

### ok()

Applies a valid 200 status to the current Request.

{% highlight php linenos %}
<?php
namespace App\Controller;

class MyController extends AppController {

    public function notFoundOverride($postID)
    {
        if (is_404()) {
            $this->ok();
        }
    }

}
?>
{% endhighlight %}


### redirect($controllerName, $action = "index", $arguments = array())

Redirects a Controller call to another during a same route.

{% highlight php linenos %}
<?php
namespace App\Controller;

class MyController extends AppController {

    use ContactTrait;

    public function show($slug)
    {

    }

    public function saveContact($slug)
    {
        if ($this->request->isPost()) {
            $this->attemptContactFormSave();
            return $this->redirect("MyController", "show", array($productLineSlug));
        }

        $this->notFound();
    }

}
?>
{% endhighlight %}
