---
layout: docs
title: Strata global object
permalink: /docs/strata/
---

## Information on the current app

You can always obtain a reference to the current instance of the MVC using  `\Strata\Strata::app`.

~~~ php
\Strata\Strata::app();
~~~

## Reading configuration value

You can access a value entered in `config/strata.php` by calling `\Strata\Strata::config()`. Specify an array path in dot notation to read the current configuration value.

~~~ php
\Strata\Strata::config('key');
\Strata\Strata::config('routes.1');
\Strata\Strata::config('routes.{n}');
~~~

## Custom values

Because the `$strata` variable declared in `config/strata.php` is a regular PHP array, you can add custom configuration values for you to use later on.

In the example below, the custom `i-want-to` key will be available throughout the application by using the following command `\Strata\Strata::config('i-want-to')`.

~~~ php
<?php
$strata = array(
    "routes" => array( /* ... */ ),
    "custom-post-types" => array( /* ... */ ),

    "i-want-to" => "rock",
);

return $strata;
?>
~~~
