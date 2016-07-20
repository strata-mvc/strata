---
layout: guidebook
title: Loading Helpers
permalink: /guidebook/1.0/digging-in/helpers/loading-helpers/
covered_tags: views, helpers
menu_group: Helpers
---

Helpers are loaded and sometimes configured from controllers. Within Strata, Helpers are not auto-loaded based on the type of controller. Meaning that even if the code loaded `CareerController` Strata will not attempt to load `CareerHelper` for you.

Helpers can be loaded using any of the following methods :

### $controller->helpers

Your application's Controllers have a public attribute named `$helpers` through which helpers are registered. A good way of having helpers loaded on each pages is to add them to the global `AppController`.

Notice the `Helper` suffix is not necessary when listing helper names.

{% highlight php linenos %}
<?php
namespace App\Controller;

use Strata\Controller\Controller as StrataController;

class AppController extends StrataController
{
    public $helpers = array(
        "Form",
        "Country",
        "Youtube",
        "Gtm"
    );
}
?>
{% endhighlight %}


### $controller->view->loadHelper()

In times when you wish to load helpers on a per-action basis, you can use a Controller's View's `loadHelper` function.

Notice the `Helper` suffix is not necessary when load helpers.

{% highlight php linenos %}
<?php
namespace App\Controller;

class BusinessController extends AppController
{
    public function before()
    {
        parent::before();

        $this->view->loadHelper('AccessoryCategory');
    }
}
?>
{% endhighlight %}


## Calling a Helper

Once the helpers are set to be instantiated by your controllers, you can use them in the template files like so:

{% highlight php linenos %}
<h1><?php the_title(); ?></h1>

<?php echo $ThumbnailHelper->render(); ?>

<article><?php the_content(); ?></article>
{% endhighlight %}
