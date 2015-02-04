---
layout: docs
title: Model taxonomies
permalink: /docs/models/taxonomies/
---

## Model with special taxonomy

Should you wish to add a taxonomy to created models you can do so using the `has` attribute in the Model's `$options` array.

~~~ php
<?php
namespace Mywebsite\Models;

use MVC\CustomPostTypes\Entity;

class Profile extends Entity
{
    public static $options = array(
        'has' => array('Mywebsite\Models\ProfileType')
    );
}
?>
~~~

This will automatically generate a taxonomy based on ProfileType, which can be configured like so :

~~~ php
<?php
namespace Mywebsite\Models;

use MVC\CustomPostTypes\TaxonomyEntity;

class ProfileType extends TaxonomyEntity
{
    const TYPE_VOLUNTEER   = "volunteer";
    const TYPE_SUBCRIBER   = "subscriber";

    public static $options = array(
        'labels'      => array(
            'name' => "Profile Types"
        )
    );
}
?>
~~~

## Additional options

Similarly to the model entities, the optional `options` attribute allows you to customize the configuration array that is sent to `register_taxonomy` internally. As long as you follow the [conventions](http://codex.wordpress.org/Function_Reference/register_taxonomy) your taxonomy will be created using these customized values, filling the missing options with their default counterparts.
