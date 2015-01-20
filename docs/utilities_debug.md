---
layout: docs
title: debug()
permalink: /docs/utilities/debug/
---

By including the MVC's bootstraper, you gain access to a global function named `debug()`. While it is mainly a styled version of `var_dump`, it still can be useful while developing.

The function accepts anything as it's first parameter. It will print the value of the variable followed by a stack trace dump.

~~~ php
<?php
debug($myvar);
?>
~~~

If you had previously declared your own global function named `debug`, we will not inject our version of the function.
