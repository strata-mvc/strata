---
layout: guidebook
title: Loggers
permalink: /guidebook/1.0/digging-in/debugging/loggers/
covered_tags: logs
menu_group: debugging
---

Strata comes with 4 types of loggers :

* **Console**: Outputs to a shell
* **File**: Outputs to a file
* **Null**: Catch all loggers similar to sending output to /dev/null

Loggers are configured at runtime in the global configuration file. Values set under the `logging` key of the configuration array in `~/config/strata.php` will be matched against the known logger types and made available through the global `Strata\Strata::app()->getLogger($name)` function.

## Configuring

Here's how you would configure a `FileLogger`. Add your custom logger to the configuration file. You may pass the optional `name` key as optional configuration to name your logger. Without a `name`, the logger will automatically be named like it's type.

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

?>
{% endhighlight %}

## Programmatically declare a logger

Should you need a logger that you do not wish to declare in your configuration file, you may obtain an object reference from `LoggerBase`.
what
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
?>
{% endhighlight %}


## Sending a message

You can obtain a reference to your custom logger using `Strata\Strata::app()->getLogger($name)`. The obtained object exposes a `log($message, $context)` method through which you can send your messages.

To build on the previous `FileLogger` example, here's how you would log a message:

{% highlight php linenos %}
<?php
    $logger = Strata\Strata::app()->getLogger("MyCustomLogger");
    $logger->log("Hello World!", "App");
?>
{% endhighlight %}
