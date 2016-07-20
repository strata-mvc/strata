---
layout: guidebook
title: Matched permalink routing
permalink: /guidebook/1.0/digging-in/routing/matched-permalink-routing/
menu_group: routing
---

Matched routing occurs when the current page URL is compared to an exact string and are manually routed to a Controller and it's intended action.

Routes of this type must be set as part of Strata's configuration array in `~/config/strata.php` under the `routes` key :

{% highlight php linenos %}
<?php
$strata = array(
    "routes" => array(

        array('POST',       '/wp/wp-admin/admin-ajax.php',  'AjaxController'),
        array('GET|POST',   '/company/',                    'CompanyController#index'),
        array('GET|POST',   '/2014/12/hello-world/',        'HelloworldController#show'),

    )
);
?>
{% endhighlight %}

AS you can see, a matched route is represented by an array consisting of 3 indexes :

* The supported **request method**
* The actual **url** to match (completely or partially)
* The destination **controller** name and **action** method.

## Complex matching patterns

Under the hood Strata uses AltoRouter to match routes. As long as you specify rules using their array notation it will work as intended in Strata. More information about how you can customize the rules can be found on [Altorouter's documention page](https://github.com/dannyvankooten/AltoRouter).


## On common destinations

You can map multiple URLs to the same controller, therefore you can expect the following example to work the way you would think :

{% highlight php linenos %}
<?php
$app = array(
    "routes" => array(

        array('GET|POST', '/en/original-english-url/', 'HelloworldController#view')
        array('GET|POST', '/fr/traduction-francaise/', 'HelloworldController#view')
        array('GET|POST', '/es/hola-amigo/', 'HelloworldController#view')

    )
);
?>
{% endhighlight %}
