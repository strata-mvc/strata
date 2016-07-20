---
layout: guidebook
title: Using the debugger
permalink: /guidebook/1.0/digging-in/debugging/using-the-debugger/
covered_tags: development, debug
menu_group: debugging
---

Strata declares a global function named `debug()`. This function is very useful while developing because it allows you to dump object values both in the HTML and in the logs.

Should you have previously declared your own global function named `debug` before Strata is executed, we will not inject our version of the function.

The function accepts anything for as many parameters as you would like.

{% highlight php linenos %}
<?php
    $bar = "bar";
    debug(null, "foo", $bar);
?>
{% endhighlight %}

Would print the following in your logs:

![Debug output](/images/debug-sample.png)



