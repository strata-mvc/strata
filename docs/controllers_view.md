---
layout: docs
title: Controller Request
permalink: /docs/controllers/view/
---

On each instantiation of the Controller object, a linked View object is created. It is the interface that handles how generated content is handled.

## Setting view variables

To expose a variable to the regular Wordpress templating, use the class' `set($key, $mixed)` method. This exposes the value to the templating engine so you can use them in Wordpress' template files.

In the controller :

~~~ php
<?php
    $this->view->set("song", $mysong);
?>
~~~

In a template file :

~~~ php
<?php if (isset($song)) : ?>
    <p><?php echo $song->post_title; ?></p>
<?php endif; ?>
~~~

## Rendering

### Loading view templates

Frequently, you will will have to load a template file containing HTML. To load a template file in which all view variables are instantiated, you can use the `loadTemplate` function.

~~~ php
<?php
namespace Mywebsite;

use Mywebsite\Model\Profile;

class AdminController extends \Mywebsite\Controller\AppController {

    public function volunteersDashboardMetabox()
    {
        $this->set('nbVolunteers', Profile::repo()->getVolunteerCount());

        $this->view->render(array(
            "content" => $this->view->loadTemplate('admin/dashboard/profiles')
        ));
    }
}
?>
~~~

### JSON and object types

The value of `content` does not have to be of type `string`. You can return an Array and the value will automatically be converted to JSON data.

~~~ php
<?php

namespace Mywebsite;

use Mywebsite\Model\Profile;

class AjaxController extends \Mywebsite\Controller\AppController {
    public function getProfiles()
    {
        $this->view->render(array(
            "content" => Profile::repo()->findAll())
        ));
    }
}
?>
~~~

## On file downloads

By default, calling `render()` will end the current php process and it will prevent the rendering of the full Wordpress template stack (which we don't need as we are printing partial data). This function allows you to specify the content-type of the returned object as well as additional values to PHP's `header()` call. It is easy to set up file downloads by entering known PHP header arguments:

~~~ php
<?php
namespace Mywebsite;

use Mywebsite\Model\Form\CSVExportForm;

class FileController extends \Mywebsite\Controller\AppController {

    public function downloadcsv()
    {
        $form = new CSVExportForm();
        $this->view->render(array(
            "Content-type"          => 'text/csv',
            "Content-disposition"   => "attachment;filename=" . $form->getCSVFilename(),
            "content"               => $form->filterResultsToCSV()
        ));
    }
}
?>
~~~

## On rendering in the admin

Hooks into the backend will be rendered mid-page and not before the first line of HTML is printed because of how Wordpress allows integration. This means that if your action needs to print something, it is important that you do not stop the original page rendering when calling the controller's `render()` method. Passing `false` to the `end` parameter of the function will allow the request to complete normally.

~~~ php
<?php
namespace Mywebsite\Controller;

class AdminController extends \Mywebsite\Controller\AppController {

    public function secondProfileAction()
    {
        $this->view->render(array(
            "content" => "This is content that will print on the page, but the admin's footer will appear.",
            "end" => false
        ));
    }
}
?>
~~~

