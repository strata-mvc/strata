---
layout: docs
title: EmailLoader
permalink: /docs/utilities/emailloader/
---

`EmailLoader` allows you to build dynamic templates for sending emails with Wordpress. It allows you to load template files located in `/templates/emails/` under your theme's directory.

Two parameters can be sent to `EmailLoader::loadTemplate`.

* The name of the template file (minus the .php extension).
* an optional array of values to send to the template


In the case of this typical call in a controller :

~~~ php
namespace Mywebsite\Controllers;

use MVC\Controller;
use MVC\Emails\EmailLoader;

class ChildrenController extends Controller {

    public function create()
    {
        $values = array(
            "userId" => 10,
            "color" => "green"
        );
        $parentNotification = EmailLoader::loadTemplate('new-student.parent.notification', $values);
        $schoolNotification = EmailLoader::loadTemplate('new-student.school.notification', $values);

        // Send confirmation emails to school admins and parent.
        EmailLoader::enableHTML();
        wp_mail($parentEmail,  __("Signup Confirmation", \MVC\Mvc::config('key')), $parentNotification, array('Reply-To' => 'no-reply@domain.com'));
        wp_mail($shcoolEmails, __("Signup Confirmation", \MVC\Mvc::config('key')), $schoolNotification, array('Reply-To' => 'no-reply@domain.com'));
        EmailLoader::disableHTML();
    }
}
~~~

You will be able to format an email template like this, in `/templates/emails/new-student.school.notification.php` under your theme's directory.

~~~ html
<ul>
    <li>The current user id is : <?php echo $userId; ?></li>
    <li>The current color is : <?php echo $color; ?></li>
</ul>
~~~
