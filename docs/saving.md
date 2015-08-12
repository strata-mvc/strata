---
layout: docs
title: Saving user data
permalink: /docs/saving/
---

Saving data is an important part of a complex web application. Strata allows you to save data outside of the regular Wordpress scope (expicitely, outside of the Wordpress backend).

To ease and automate this process, our tools allow the building of dynamic forms and automated validations on posted values.

The are 4 steps to this process :

* Setting up the controller to prepare and catch form data
* Adding the form in the view
* Saving the data through the model object.


{% include workinprogress.html %}

# Setting up the controller

Implying you have [created routing routes](/docs/routes/) that will reach the `create` action, you can now start crafting your tools. You need to declare a model entity and a form object that will both be used to validate posted data.

Upon each page load, when `$form->process()` is called, the form will validate values in the `$_POST` array and look for variables that match the attributes of the entities passed to it. This allows you to validate multiple entities in the same form.

Because a form is considered completed once form steps are completed (if there were any) and when all the attributes have passed the automated validation tests, we can save the form by passing it to the model entity.


~~~ php
<?php
namespace App\Controller;

use App\Model\Song;
use App\Model\Form\SongForm;

class SongsController extends AppController {

    public $helpers = array(
        "Form"
    );

    public function edit()
    {
        $postId = 15;
        $song = new SongEntity($postId);
        $this->view->set("song", $song);

        if ($this->request->isPost()) {
            $data = $this->request->data();
            if ($song->validates($data)) {
                $songId = $song->save($song);
                $this->view->set("completed", true);
            }
        }
    }
}
?>
~~~

### Building a form in a template file

This is an example of a form that would have steps. `$FormHelper->create($song)` will print out the form tag , taking care of the basic form attributes. If you need to customize an html attribute, pass it as a configuration array: `$FormHelper->create($song, ["class" => "song-form"])`.

~~~ html

<?php if (isset($completed) && $completed) : ?>

<h1>Thank you!</h1>
<p>The form has been saved.</p>

<?php else : ?>

<?php echo $FormHelper->create($song); ?>

<?php echo $FormHelper->input('artist', array("label" => "Artist", "class" => "big long", "type" => "select", "choices" => $artists)); ?>

<?php echo $FormHelper->input('title', array("label" => "Song Title")); ?>

<!-- Imagine an american phone number separated in multiple fields. Passing inline-errors => false allows
    you to decide how error messages are printed. In this example, the message is printed after all the fields and not on each fields. -->

<?php echo $FormHelper->input('telephone_area', array("label" => "Main phone number", "class" => "phone area", "size" => "3",  "maxlength" => "3")); ?>

<?php echo $FormHelper->input('telephone_centraloffice', array("class" => "phone office", "size" => "3",  "maxlength" => "3")); ?>
     -
<?php echo $FormHelper->input('telephone_station', array("class" => "phone station", "size" => "4",  "maxlength" => "4")); ?>

Ext :
<?php echo $FormHelper->input('telephone_ext', array("class" => "phone ext")); ?>


<!-- Any arguments you pass on as configurable values to the input function will be added to the final html without
    any validation. You could therefore achieve something like this, where you pass custom javascript listeners. -->

<?php echo $FormHelper->input('email', array("onCopy" => "return false", "onDrag" => "return false", "onDrop" => "return false",  "onPaste" => "return false", "autocomplete" => "off")); ?>

<?php echo $FormHelper->input('email_confirm', array("onCopy" => "return false", "onDrag" => "return false", "onDrop" => "return false",  "onPaste" => "return false", "autocomplete" => "off")); ?>

<?php echo $FormHelper->submit(array("label" => "Submit!", "class" => "btn blue submit")); ?>

<?php echo $FormHelper->end(); ?>

<?php endif; ?>
~~~


## Saving

The example bellow saves a new `Song` with the song title as post title and assigns it a published state. Once we have a post id, we can loop though the model's attribute list to assign the posted values to the post's meta or, in this case, Advanced Custom Fields.

~~~ php
<?php
namespace App\Model;

class Song extends AppCustomPostType{
{
    public $attributes = array(
        "title",                    => array("validations" => array("required")),
        "artist"                    => array("validations" => array("required", "postExists")),
        "telephone_area"            => array("validations" => array("required", "numeric", "length" => array("min" => 3, "max" => 3))),
        "telephone_centraloffice"   => array("validations" => array("required", "numeric", "length" => array("min" => 3, "max" => 3))),
        "telephone_station"         => array("validations" => array("required", "numeric", "length" => array("min" => 4, "max" => 4))),
        "telephone_ext"             => array("validations" => array("numeric")),
    );

    public function save($data)
    {

        $ourData = $data[$this->getInputName()];

        // Create the post type entity
        $songId = self::create(array(
            'post_title'    => trim($ourData['title']),
            'post_status'   => 'publish',
        ));

        // We encourage the use of Advanced custom field to save additional meta-data
        // in the custom post types. This example illustrates how one could decide to implement it:
        //
        // Loop through all supported attributes and set the ACF field linked
        // to the entity. The corresponding keys have to have been set in WP's backend.
        foreach (array_keys($this->getAttributes()) as $attribute) {
            update_field($attribute,  $ourData[$attribute], $songId);
        }

        return $songId;
    }
}
?>
~~~
