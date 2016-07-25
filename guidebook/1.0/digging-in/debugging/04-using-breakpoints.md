---
layout: guidebook
title: Using breakpoints
permalink: /guidebook/1.0/digging-in/debugging/using-breakpoints/
covered_tags: development, breakpoint
menu_group: debugging
---

At any point during the execution of the request, you can stop the process by entering a breakpoint from which to debug your application.

The breakpoint will open a new console in which you can try different operations based on the current state of the application.

To declare a breakpoint invoke the global `breakpoint();` function from anywhere within your code.

Should you have previously declared your own global function named `breakpoint` before Strata is executed, Strata's version will not be injected over the predefined one.

{% highlight php linenos %}
<?php
namespace App\Controller;

use App\Model\Taxonomy\ExpertAdviceType;

class ExpertAdviceTypeController extends AppController
{
    public function index()
    {
        breakpoint();

        $adviceTypes = ExpertAdviceType::repo()
            ->where("hide_empty", false)
            ->fetch();

        $this->view->set("adviceTypes", $adviceTypes);
    }
?>
{% endhighlight %}

It will bring up a console through which you can debug the current context.

![Breakpoint output](/assets/images/breakpoint-sample.png)
