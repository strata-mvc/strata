---
layout: guidebook
title: Dynamically matched routing
permalink: /guidebook/1.0/digging-in/routing/dynamically-matched-routing/
menu_group: routing
---

You may dynamically declare controllers and actions using regular expression keys in the URL serving as matching pattern. If you go as far as dynamically setting the controller and action, you no longer have to set the third destination parameter in the route's configuration as they are implicitly defined.

Note that this process only compares strings to validate the validity of a URL. Wordpress needs to know that this is a valid destination for a post, page or taxonomy otherwise you will always be forwarded to a 404 even if the Controller's action is correctly invoked.

Here are some examples of how to use dynamic matches :

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
$strata = array(
    "routes" => array(
        array('GET', '/about-us/team/[:slug]/', 'TeamController#show'),
        array('GET|POST', '/[*:controller]/[*:action]'),
        array('GET|POST', '/[*:controller]/'),
    )
);
?>
{% endhighlight %}
{% include terminal_end.html %}

You can catch URL parameters to variables with the use of `[*:varname]`. A variable caught that way would then be available as first parameter of the callback function inside the controller.

When you use predefined variable names as `controller` and `action`, Strata will automatically use the caught value to determine the correct controller invocation.

Otherwise these values collected are sent as parameters of the action's function. In the following example a custom slug will be dynamically passed to the action.

Assuming you have created one top level page with two children :

* Our company (/our-company/)
    * People (/our-company/people/)
    * Contact (/our-company/contact/)

With the following set of routing rules :

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
$strata = array(
    "routes" => array(
        array('GET', '/our-company/', 'CompanyController#index'),
        array('GET', '/our-company/[*:subpage]/', 'CompanyController#show'),
    )
);
?>
{% endhighlight %}
{% include terminal_end.html %}

The last rule in the previous example will trigger on calls to `/our-company/people/` and `/our-company/contact/`. These request will be routed to the `show` method of the controller `CompanyController` with the matched `subpage` as first parameter.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Controller;

class CompanyController extends AppController {

    public function index()
    {

    }

    public function show($subpage = null)
    {
        if (!is_null($subpage)) {
            $this->view->set("currentSubPageKey", $subpage);
        }
    }

}
?>
{% endhighlight %}
{% include terminal_end.html %}

Resourced-based routes will work very similarly to this example. However instead of matching `Pages` or any type of hard-coded URL, they will be automatically based on custom post type slugs. You should favor Resourceful routes when applicable.
