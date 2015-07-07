---
layout: docs
title: Strata global object
permalink: /docs/strata/
---

## Reference the current instance

You can always obtain a reference to the current instance of the MVC using  `\Strata\Strata::app()`.

~~~ php
\Strata\Strata::app();
~~~

## Reading configuration value

You can access a value entered in `config/strata.php` by calling `\Strata\Strata::config()`. Specify an array path in dot notation to read the current configuration value.

~~~ php
\Strata\Strata::config('namespace');
\Strata\Strata::config('routes.1');
\Strata\Strata::config('routes.{n}');
~~~

The same way, you can access custom values added to the configuration file :

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

~~~ php
echo \Strata\Strata::config('i-want-to');
// ~ "rock"
~~~
