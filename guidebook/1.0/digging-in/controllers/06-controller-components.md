---
layout: guidebook
title: Controller Components
permalink: /guidebook/1.0/digging-in/controllers/controller-components/
covered_tags: controller, component, trait, inheritance
menu_group: controllers
---

Strata believes in the "thin controller - fat models" theorem in that Controllers should not contain much code at all where as Model should hold most of the application logic.

A method of inheriting behavior and simplifying Controller classes is to use [PHP Traits](http://php.net/manual/en/language.oop5.traits.php).

You may then place all function which to not map to a routed action as part of a trait.

{% highlight php linenos %}
<?php
namespace App\Controller;

use App\Controller\Component\ContactTrait;

class ContactController extends AppController
{
    use ContactTrait;

    public function before()
    {
        parent::before();

        $this->setupContactForm();
    }

    public function send()
    {
        if ($this->request->isPost()) {
            $this->attemptContactFormSave();
            return $this->redirect("ContactController", "index");
        }

        $this->notFound();
    }
}
?>
{% endhighlight %}

The `ContactTrait` can therefore be used by any Controller of your application which will gain the `setupContactForm()` and `attemptContactFormSave()` methods.

{% highlight php linenos %}
<?php
namespace App\Controller\Component;

use App\Model\ContactApplication;
use Exception;

/**
 * The contact trait allows a controller to automate the saving of a
 * contact-form type of object.
 */
trait ContactTrait
{
    /**
     * A model entity
     * @var App\Model\Entity\AppModelEntity
     */
    protected $contact;

    /**
     * Prepares the from object and assigns it to the current controller.
     */
    protected function setupContactForm()
    {
        $this->contact = ContactApplication::getEntity();
        $this->view->set("contactApplication", $this->contact);
    }

    /**
     * Attempts to save the object current set to the Controller's $contact attribute.
     * Prepares the variables for use in the views and sets GTM events.
     * @return bool State of success
     * @throws \Exception
     */
    protected function attemptContactFormSave()
    {
        if ($this->request->requestValidates($this->contact, $this->contact->getHoneypotName())) {
            try {
                $this->contact->assignRequest($this->request);

                $status = $this->contact->validates() && $this->contact->saveAll();
                $this->view->set("contactSuccess", $status);
                return $status;
            } catch (Exception $e) {
                $this->view->set("contactErrorMessage", $e->getMessage());
            }
        } else {
            $this->view->set("contactErrorMessage", "This request does not validate.");
        }
    }

}
?>
{% endhighlight %}
