---
layout: docs
title: Mvc global object
permalink: /docs/mvc/
---

## Information on the current app

You can always obtain a reference to the current instance of the MVC using  `Mvc::app`.

~~~ php
\MVC\Mvc::app();
\MVC\Mvc::app()->getNamespace();
~~~

## Reading configuration value

Accessing a value entered in `app.php` is handled by `Mvc::config` at which an array path in dot notation can be passed to read the current configuration value.

~~~ php
\MVC\Mvc::config('key');
\MVC\Mvc::config('routes.1');
\MVC\Mvc::config('routes.{n}');
~~~

## Custom values

Because the `$app` variable declared in `app.php` is basically just an array, you can add custom values to the configuration array that you can use later on.

In the example below, the custom `i-want-to` key will be available throughout the application using `Mvc::config`.

~~~ php
<?php
$app = array(
    "key" => "Mywebsite",

    "routes" => array( /* ... */ ),
    "custom-post-types" => array( /* ... */ ),

    "i-want-to" => "rock",
);
?>
~~~

~~~ php
<?php
    debug(\MVC\Mvc::config('i-want-to'));
?>
~~~
