---
layout: docs
title: EmailLoader
permalink: /docs/utilities/emailloader/
---

`EmailLoader` allows you to build dynamic templates for sending emails with Wordpress. It allows you to load template files located in `/templates/emails/` under your theme's directory.

Two parameters can be sent to `EmailLoader::loadTemplate` :

* The __name__ of the template file (minus the .php extension).
* an optional array of __values__ used in the template


In the case of this typical call in a controller you will be able to format an email using a template located at `/templates/emails/new-student.song.notification.php` under your theme's directory.

~~~ php
<?php
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
        $songNotification = EmailLoader::loadTemplate('new-student.song.notification', $values);

        // Send confirmation emails to song admins and parent.
        EmailLoader::enableHTML();
        wp_mail($parentEmail,  __("Signup Confirmation", \MVC\Mvc::config('key')), $parentNotification, array('Reply-To' => 'no-reply@domain.com'));
        wp_mail($shcoolEmails, __("Signup Confirmation", \MVC\Mvc::config('key')), $songNotification, array('Reply-To' => 'no-reply@domain.com'));
        EmailLoader::disableHTML();
    }
}
?>
~~~

The template file named `new-student.parent.notification.php` could then use the variables send in the `$values` array.

~~~ html
<ul>
    <li>The current user id is : <?php echo $userId; ?></li>
    <li>The current color is : <?php echo $color; ?></li>
</ul>
~~~
