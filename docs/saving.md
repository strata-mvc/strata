---
layout: docs
title: Saving user data
permalink: /docs/saving/
---

Saving data is an important part of a complex web application. Strata allows you to save data outside of the regular Wordpress scope (expicitely, outside of the Wordpress backend).

To ease and automate this process, our tools allow the building of dynamic forms and automated validations on posted values.

The are 4 steps to this process :

* Setting up the controller to prepare and catch form data
* Creating the form object that will contain form logic
* Adding the form in the view
* Saving the data through the model object.


{% include workinprogress.html %}

# Setting up the controller

Your controller can expose the form using a shortcode. In the following example, we declare a shortcode labeled `[songform]` that will print out the form's generated html. The shortcode callbacks are discussed [in the Controller documentation](/docs/controllers/#shortcodes-and-exposing-actions).

Implying you have [created routing routes](/docs/routes/) that will reach the `create` action, you can now start crafting your tools. You need to declare a model entity and a form object that will both be used to validate posted data.

Upon each page load, when `$form->process()` is called, the form will validate values in the `$_POST` array and look for variables that match the attributes of the entities passed to it. This allows you to validate multiple entities in the same form.

Because a form is considered completed once form steps are completed (if there were any) and when all the attributes have passed the automated validation tests, we can save the form by passing it to the model entity.


~~~ php
<?php
namespace Mywebsite\Controller;

use Mywebsite\Model\Song;
use Mywebsite\Model\Form\SongForm;

class SongsController extends \Mywebsite\Controller\AppController {

    public $shortcodes = array("songform" => 'getFormTemplate');
    protected $_form = null;

    public function getFormTemplate()
    {
        return $this->_form->toHtml();
    }

    public function create()
    {
        $song           = new Song();
        $form           = new SongForm();
        $this->_form    = $form;

        $form->process(array($song));

        if ($form->isCompleted()) {
            $songId = $song->saveForm($form->getHelper());
        }
    }
}
?>
~~~

## Creating the form object

Each one of your forms must be linked to an object implementing `Strata\Model\Form`. You custom form objects should be located under your project's model's forms directory : `src/model/form/SongForm.php`.

The role of this object is to declare the context of the form as well as grant functions that can help controllers handle the form data. For example, here the form will have 5 steps to it and the Form object will look for template files prefixed with `songs` when building the html.

`hasATitle` is a method that could be used by a controller to differentiate between to special cases where only the form should be in on the details.

~~~ php
<?php
namespace Mywebsite\Model\Form;

class SongForm extends \Strata\Model\Form
{
    public function init($options = array())
    {
        return parent::init(array(
            "stepsQty"  => 5, // optionally declare the form will have 5 steps
            "formKey"   => 'songs' // this key is used when loading templating files
        ));
    }

    public function hasATitle()
    {
        $helper = $this->getHelper();
        return $helper->hasPostedValue("song.title");
    }
}
?>
~~~

## Preparing the view

Though the form is loaded through a shortcode, there will still be templating involved. In the controller above, when `$this->_form->toHtml()` is called the form object will try to load template file located under `[current_theme]/templates/forms/*`. The name of the template is based on the `formKey` parameter sent in when constructing the `SongForm` object.

Should your form have steps, the template's file name will have the current step name to it: `[current_theme]/templates/forms/songs.step1.php`.

If not, then only the `formKey` is used : `[current_theme]/templates/forms/songs.php`.

When your form is considered completed, the Form object will load the completed template : `[current_theme]/templates/forms/songs.completed.php`.

### Building a form in a template file

`form->toHtml()` will automatically declare a view variable with the name of the current form for you. You can in turn use it in your template file to generate the form fields.

This is an example of a form that would have steps. `$SongForm->create()` will print out the form tag , taking care of the basic form attributes. If you need to customize an html attribute, pass it as a configuration array: `array("class" => "song-form")`.

If your form has steps you can print out the step labels. It's important to place this after `create()` because the steps generate a button that allows going backwards that submits the form in order to remember posted values.

~~~ html
<?php echo $SongForm->create(); ?>

<?php if ($SongForm->hasSteps()) : ?>
    <section class="form-steps">
        <?php echo $SongForm->getStepsHtml(array("titles" => array("Song", "Information", "Stream", "Confirmation"))); ?>
    </section>
<?php endif ?>

<section class="form-content">
    <label for="<?php echo $SongForm->id('song[artist]'); ?>">Artist <span class="required">*</span></label>
    <?php echo $SongForm->input('song[artist]', array("class" => "big long", "type" => "select", "choices" => $artists)); ?>

    <label for="<?php echo $SongForm->id('song[title]'); ?>">Song Title <span class="required">*</span></label>
    <?php echo $SongForm->input('song[title]'); ?>


    <!-- Imagine an american phone number separated in multiple fields. Passing inline-errors => false allows
        you to decide how error messages are printed. In this example, the message is printed after all the fields and not on each fields. -->

    <label for="<?php echo $SongForm->id('song[telephone_area]'); ?>"><?php _e("Main phone number", PROJECT_KEY); ?><span class="required">*</span></label>
    <?php echo $SongForm->input('song[telephone_area]', array("class" => "phone area", "size" => "3",  "maxlength" => "3", "inline-errors" =>false)); ?>
    <?php echo $SongForm->input('song[telephone_centraloffice]', array("class" => "phone office", "size" => "3",  "maxlength" => "3", "inline-errors" =>false)); ?>
         -
    <?php echo $SongForm->input('song[telephone_station]', array("class" => "phone station", "size" => "4",  "maxlength" => "4", "inline-errors" =>false)); ?>

    Ext :
    <?php echo $SongForm->input('song[telephone_ext]', array("class" => "phone ext", "inline-errors" =>false)); ?>

    <?php if( $SongForm->hasErrors("song[telephone_area]") || $SongForm->hasErrors("song[telephone_centraloffice]") || $SongForm->hasErrors("song[telephone_station]") ) : ?>
       <ul class="inline-errors">
        <li class="required">Please enter a valid phone number.</li>
       </ul>
    <?php endif; ?>



    <!-- Any arguments you pass on as configurable values to the input function will be added to the final html without
        any validation. You could therefore achieve something like this, where you pass custom javascript listeners. -->

     <label for="<?php echo $SongForm->id('song[email]'); ?>">E-mail<span class="required">*</span></label>
     <?php echo $SongForm->input('song[email]', array("onCopy" => "return false", "onDrag" => "return false", "onDrop" => "return false",  "onPaste" => "return false", "autocomplete" => "off")); ?>

     <label for="<?php echo $SongForm->id('email_confirm'); ?>">Confirm E-mail<span class="required">*</span></label>
     <?php echo $SongForm->input('email_confirm', array("onCopy" => "return false", "onDrag" => "return false", "onDrop" => "return false",  "onPaste" => "return false", "autocomplete" => "off")); ?>



    <!-- you wouldn't have to test for steps here as you are supposed to know if the form does. This is only to illustrate
    how one would print submit buttons depending on use cases -->

    <?php if ($SongForm->hasSteps()) : ?>
        <?php echo $SongForm->next(array("label" => "Next step", "class" => "btn blue next")); ?>
        <?php echo $SongForm->previous(array("label" => "Previous step", "class" => "btn blue prev")); ?>
    <?php else : ?>
       <?php echo $SongForm->submit(array("label" => "Submit!", "class" => "btn blue submit")); ?>
    <?php endif; ?>

</section>

<?php echo $SongForm->end(); ?>
~~~

The completed template doesn't really have anything else than a formatted message to the user.

~~~ html
<h1>Thank you!</h1>
<p>The form has been saved.</p>
~~~

## Saving

The controller sent the Form's helper object to the model's `saveForm` function. The helper's job is to fetch the posted values. Because the method is called after validating `isComplete()` is `true`, we can assume the posted values have passed all validation.

The example bellow saves a new `Song` with the song title as post title and assigns it a published state. Once we have a post id, we can loop though the model's attribute list to assign the posted values to the post's meta or, in this case, Advanced Custom Fields.

~~~ php
<?php
namespace Mywebsite\Model;

class Song extends \Strata\Model\CustomPostType\Entity {
{
    public $attributes = array(
        // Proofed info
        "title",                    => array("validations" => array("required")),
        "artist"                    => array("validations" => array("required", "postExists")),
        "telephone_area"            => array("validations" => array("required", "numeric", "length" => array("min" => 3, "max" => 3))),
        "telephone_centraloffice"   => array("validations" => array("required", "numeric", "length" => array("min" => 3, "max" => 3))),
        "telephone_station"         => array("validations" => array("required", "numeric", "length" => array("min" => 4, "max" => 4))),
        "telephone_ext"             => array("validations" => array("numeric")),
    );

    public function saveForm($formHelper)
    {
        // Create the post type entity
        $songId = self::create(array(
            'post_title'    => trim($formHelper->getPostedValue('song.title')),
            'post_status'   => 'publish',
        ));

        // We encourage the use of Advanced custom field to save additional meta-data
        // in the custom post types. This example illustrates how one could decide to implement it:
        //
        // Loop through all supported attributes and set the ACF field linked
        // to the entity. The corresponding keys have to have been set in WP's backend.
        foreach (array_keys($this->getAttributes()) as $attribute) {
            update_field($attribute, $formHelper->getPostedValue(self::key() . "." . $attribute), $songId);
        }

        return $songId;
    }
}
?>
~~~

