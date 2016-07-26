---
layout: guidebook
title: Custom Post Type Taxonomies
permalink: /guidebook/1.0/digging-in/models/custom-post-type-taxonomies/
covered_tags: models, custom-post-types, taxonomy
menu_group: models
---

## Creating the taxonomy class

To generate a taxonomy definition, you should use the automated generator provided by Strata.  It will validate your object's name and ensure it is defined following the intended conventions.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a taxonomy called `ProfileType` that will eventually helper categorize a `Profile` model:

~~~ sh
$ ./strata generate taxonomy ProfileType
~~~

It will generate a couple of files for you, including the actual taxonomy file and test suites for the generated class.

{% include terminal_start.html %}
{% highlight bash linenos %}
Scaffolding model Artist
  ├── [ OK ] src/Model/Taxonomy/ProfileType.php
  ├── [ OK ] src/Model/Entity/ProfileTypeEntity.php
  └── [ OK ] test/Model/Taxonomy/ProfileTypeTest.php
  ├── [ OK ] test/Model/Entity/ProfileTypeEntityTest.php
{% endhighlight %}
{% include terminal_end.html %}


## Loading the Taxonomy to a AppCustomPostType

Should you wish to link a taxonomy to created models you can do so using the `$belongs_to` attribute in the `AppCustomPostType`'s class. When the Custom Post Type is explicitly set to be automatically loaded by Strata in the global configuration array, Strata will also register its Taxonomies.

~~~ php
<?php
namespace App\Model;

class Profile extends AppCustomPostType
{

    public $belongs_to = array('App\Model\ProfileType');

    public $configuration = array(
        //...
    );
}
?>
~~~

This will look for a Taxonomy definition called `ProfileType`.

Similarly to the model entities, the optional `$configuration` attribute allows you to customize the configuration array that is sent to `register_taxonomy` internally. As long as you follow the [conventions](http://codex.wordpress.org/Function_Reference/register_taxonomy) your taxonomy will be created using these customized values, filling the missing options with their default counterparts.

~~~ php
<?php
namespace App\Model;

use Strata\Model\CustomPostType\Taxonomy;

class ProfileType extends Taxonomy
{
    public $configuration = array(
        'labels'      => array(
            'name' => "Profile Types"
        )
    );
}
?>
~~~
