---
layout: guidebook
title: Setting a different layout
permalink: /guidebook/1.0/digging-in/controllers/setting-different-layout/
covered_tags: controller, views, layouts
menu_group: controllers
---

When you know a Controller will cancel the normal Wordpress rendering stack because it invokes `$this->view->render()` you may apply layout to the View object through which the rendered content will be inserted.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Controller;

class MyController extends AppController
{
    public function before()
    {
        // Will look under ~/web/app/themes/[active-theme]/templates/layout/single-col-layout.php
        $this->view->setConfig("layout", "layout/single-col-layout");
    }

    public function index()
    {
        $this->view->set("title", "My custom page");

        $this->view->render(array(
            "content" => $this->view->loadTemplate("content-for-index")
        ));
    }

}
{% endhighlight %}
{% include terminal_end.html %}

The layout file must include a yield flag in order for Strata to understand where content should be injected. The flag can be printed with `Strata\View\Template::TPL_YIELD`:

{% include terminal_start.html %}
{% highlight php linenos %}
<html>
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title><?php echo $title; ?></title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body>
        <h1><?php echo $title; ?></h1>

        <?php echo Strata\View\Template::TPL_YIELD; ?>
    </body>
</html>
{% endhighlight %}
{% include terminal_end.html %}
