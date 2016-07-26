---
layout: guidebook
title: Loggers
permalink: /guidebook/1.0/digging-in/debugging/loggers/
covered_tags: logs
menu_group: debugging
---

Strata comes with 3 types of loggers :

* **Console**: Outputs to a shell
* **File**: Outputs to a file
* **Null**: Catch all loggers similar to sending output to /dev/null

Loggers are configured at runtime in the global configuration file. Values set under the `logging` key of the configuration array in `~/config/strata.php` will be matched against the known logger types and made available through the global `Strata\Strata::app()->getLogger($name)` method.

## Configuring

One would configure a `FileLogger` by declaring a custom logger in the project's configuration file. You may pass the optional `name` key as optional configuration to name your logger. In fact you may send anything you need in that array and it will become accessible within the class later on. Strata only looks for `name` however and without a `name`, the logger will automatically be named from its type.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php

$strata = array(
    // ...

    "logging" => array(
        "File" => array(
            "name" => "MyCustomLogger"
        ),
    ),

    // ...
);

return $strata;
{% endhighlight %}
{% include terminal_end.html %}

## Programmatically declare a logger

To instantiate a logger straight from another class, you may obtain an object reference from `LoggerBase`.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model\Service\Importer;

use Strata\Logger\LoggerBase;

class ImportHTMLCrawler
{
    private $logger;

    public function __construct()
    {
        $this->setupLogger();

        $this->logger->log("This <success>works</success>!", "MyContext");
    }

    private function setupLogger()
    {
        $this->logger = LoggerBase::factory('Console');
        $this->logger->initialize();
    }
}
{% endhighlight %}
{% include terminal_end.html %}


## Sending a message

You can obtain a reference to your custom logger using `Strata\Strata::app()->getLogger($name)`. The obtained object exposes a `log($message, $context)` method through which you can send your messages.

To build on the previous `FileLogger` example, here's how you would log a message:

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
    $logger = Strata\Strata::app()->getLogger("MyCustomLogger");
    $logger->log("Hello World!", "App");
?>
{% endhighlight %}
{% include terminal_end.html %}
