---
layout: docs
title: Model taxonomies
permalink: /docs/models/taxonomies/
---


## Creating the taxonomy class

To generate a taxonomy definition, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a taxonomy called for the `ProfileType`:

~~~ sh
$ bin/mvc generate taxonomy ProfileType
~~~


## Models with special taxonomy

Should you wish to link a taxonomy to created models you can do so using the `has` attribute in the Model's `$configuration` array.

~~~ php
<?php
namespace Mywebsite\Model;

class Profile extends \Strata\Model\CustomPostType\Entity
{
    public $configuration = array(
        'has' => array('Mywebsite\Model\ProfileType')
    );
}
?>
~~~

This will look for a taxonomy definition called `ProfileType`, which can be configured like so :

~~~ php
<?php
namespace Mywebsite\Model;

class ProfileType extends \Strata\Model\CustomPostType\TaxonomyEntity
{
    public $configuration = array(
        'labels'      => array(
            'name' => "Profile Types"
        )
    );
}
?>
~~~


## Additional options

Similarly to the model entities, the optional `$configuration` attribute allows you to customize the configuration array that is sent to `register_taxonomy` internally. As long as you follow the [conventions](http://codex.wordpress.org/Function_Reference/register_taxonomy) your taxonomy will be created using these customized values, filling the missing options with their default counterparts.
