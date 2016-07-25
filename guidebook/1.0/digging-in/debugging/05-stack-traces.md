---
layout: guidebook
title: Stack traces
permalink: /guidebook/1.0/digging-in/debugging/stack-traces/
covered_tags: development, stack trace
menu_group: debugging
---

Stack traces are useful when one is trying to understand the path that has been taken by the code during it's execution.

To view the current stack trace invoke the global `stackTrace();` function from anywhere within your code.

Should you have previously declared your own global function named `stackTrace` before Strata is executed, Strata's version will not be injected over the predefined one.

{% highlight php linenos %}
<?php
namespace App\Controller;

use App\Model\Taxonomy\ExpertAdviceType;

class ExpertAdviceTypeController extends AppController
{
    public function index()
    {
        stackTrace();
    }
?>
{% endhighlight %}

It will output both in your server logs and right in the html :

![Stack trace output](/assets/images/stacktrace-sample.png)
