---
layout: guidebook
title: Available Inheritance
permalink: /guidebook/1.0/digging-in/models/available-inheritance/
covered_tags: models, oop, inheritance
menu_group: models
---

The methods made available through `AppTaxonomy`, `AppModel` and `AppCustomPostType` are also available to classes seeking representation of recurring Wordpress concepts like the `Post` and `Page`.

Strata offers predefined classes from which to inherit in order for your models to gain query and factory methods.

## Category

Represents a `Post` category. The complete API definition for this class can be found on our [Api](/api/1.0/classes/Strata_Model_Taxonomy_Category.html).

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model;

use Strata\Model\Taxonomy\Category as StrataCategory

class Category extends StrataCategory
{

    // ...

}
?>
{% endhighlight %}
{% include terminal_end.html %}

## Post

Represents a `Post` object. The complete API definition for this class can be found on our [Api](/api/1.0/classes/Strata_Model_Post.html).

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model;

use Strata\Model\Post as StrataPost

class Post extends StrataPost
{

    // ...

}
?>
{% endhighlight %}
{% include terminal_end.html %}

It can also be applied to a custom `Page` object. You should then override `getWordpressKey()` in order for it to understand the `page` post type.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model;

use Strata\Model\Post as StrataPost

class Page extends StrataPost
{
    public function getWordpressKey()
    {
        return "page";
    }

    // ...

}
?>
{% endhighlight %}
{% include terminal_end.html %}


## User

Represents a `User` object. The complete API definition for this class can be found on our [Api](/api/1.0/classes/Strata_Model_User.html).

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model;

use Strata\Model\User as StrataUser

class User extends StrataUser
{

    // ...

}
?>
{% endhighlight %}
{% include terminal_end.html %}
