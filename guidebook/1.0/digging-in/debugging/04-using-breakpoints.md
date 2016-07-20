---
layout: guidebook
title: Using breakpoints
permalink: /guidebook/1.0/digging-in/debugging/using-breakpoints/
covered_tags: development, breakpoint
menu_group: debugging
---

At any point during the execution of the request, you can stop the process on a breakpoint from which to debug your application.

The breakpoint will open a new console in which you can try different operations based on the current code state.

To declare a breakpoint, use the global `breakpoint();` function from anywhere within your code.

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

![Breakpoint output](/images/breakpoint-sample.png)
