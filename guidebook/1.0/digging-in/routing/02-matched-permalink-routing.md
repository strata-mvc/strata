---
layout: guidebook
title: Matched permalink routing
permalink: /guidebook/1.0/digging-in/routing/matched-permalink-routing/
menu_group: routing
---

Matched routing occurs when the current page URL is compared to an exact string and are manually routed to a Controller and it's intended action.

Routes of this type must be set as part of Strata's configuration array in `~/config/strata.php` under the `routes` key :

{% include terminal_start.html %}
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
{% include terminal_end.html %}

AS you can see, a matched route is represented by an array consisting of 3 indexes :

* The supported **request method**
* The actual **url** to match (completely or partially)
* The destination **controller** name and **action** method.

## Complex matching patterns

Under the hood Strata uses AltoRouter to match routes. As long as you specify rules using their array notation it will work as intended in Strata. More information about how you can customize the rules can be found on [Altorouter's documention page](https://github.com/dannyvankooten/AltoRouter).
