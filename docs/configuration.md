---
layout: docs
title: Configuration object
permalink: /docs/configuration/
---

## Information on the current app

You can always call out the global reference to the current instance of the MVC using  `Mvc::app`.

~~~ php
\MVC\Mvc::app();
\MVC\Mvc::app()->getNamespace();
~~~

## Reading configuration value

Accessing a value entered in `app.php` is handled by `Mvc::config` at which a dot notation path string can be passed to read the current configuration value.

~~~ php
\MVC\Mvc::config('key');
\MVC\Mvc::config('routes.1');
\MVC\Mvc::config('routes.{n}');
~~~

## Custom values

Because the `$app` variable declared in `app.php` is basically just an array, you can sent out custom values that you can use later on.

The custom `i-want-to` key will be available throughout the application using `Mvc::config`.

~~~ php
$app = array(
    "key" => "Mywebsite",

    "routes" => array( /* ... */ ),
    "custom-post-types" => array( /* ... */ ),

    "i-want-to" => "rock",
);
~~~

~~~ php
debug(\MVC\Mvc::config('i-want-to'));
~~~
