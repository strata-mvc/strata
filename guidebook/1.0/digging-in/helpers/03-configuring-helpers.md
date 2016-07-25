---
layout: guidebook
title: Helper configuration
permalink: /guidebook/1.0/digging-in/helpers/configuring-helpers/
covered_tags: views, helpers, configuration
menu_group: Helpers
---

Whether it's included from a Controller's `$helpers` attribute or by using `$this->view->loadHelper()`, Helpers accept a configuration array that is sent to the helper's constructor.

You can send values that you can reuse afterwards within the Helper. The only value Strata will actively look for is for a `name`. If it is sent as part of the configuration, the helper will be declared as the supplied variable name in the view files. Otherwise the variable name in the view is always suffixed with `Helper`.

{% highlight php linenos %}
<?php
namespace App\Controller;

class ArtistController extends AppController {

    public $helpers = array(
        "Thumbnail",
        "Acf" => array(
            "name" => "Acf",
            "foo" => "bar",
        )
    );

}
{% endhighlight %}

{% highlight php linenos %}
<?php
namespace App\Controller;

class ArtistController extends AppController {

    public function before()
    {
        parent::before();

        $this->view->loadHelper("Calendar", array("numberOfDays" => 5));
    }
}
{% endhighlight %}
