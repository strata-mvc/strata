---
layout: guidebook
title: Common Behaviors
permalink: /guidebook/1.0/digging-in/models/common-behaviour/
covered_tags: models, custom-post-types, behavior, oop
menu_group: models
---

Strata Models should inherit either one of `App\Model\AppModel` or `App\Model\AppCustomPostType`. Instead of passing behavior between model classes through inheritance, it should be applied using [PHP Traits](http://php.net/manual/en/language.oop5.traits.php).

Start by building your behavior as a Trait and place it under `~/src/Model/Behavior/`. Here we add more flexible query methods under the `QueriablePostTrait`:

{% include terminal_start.html %}
{% highlight php linenos %}
<?php

namespace App\Model\Behavior;

trait QueriablePostTrait {

    public function byName()
    {
        return $this
            ->orderby("post_title")
            ->direction("ASC");
    }

    public function published()
    {
        return $this->status("publish");
    }

    public function byRecency()
    {
        return $this
            ->orderby("creation_date")
            ->direction("DESC");
    }

    public function byMenuOrder()
    {
        return $this
            ->orderby("menu_order")
            ->direction("ASC");
    }
}

{% endhighlight %}
{% include terminal_end.html %}


By adding the Trait to either one of your project's model classes or the common `AppModel` class it will gain the prepared behavior.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model;

use App\Model\Behavior\QueriablePostTrait;
use Strata\Model\Model as StrataModel;

class AppModel extends StrataModel
{
    use QueriablePostTrait;

}
?>
{% endhighlight %}
{% include terminal_end.html %}
