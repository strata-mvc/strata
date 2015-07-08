---
layout: docs
title: EmailLoader
permalink: /docs/utilities/emailloader/
---

<p class="warning">This class has not been made singular and may be so in the near future. It is possible code will break because of this change.</p>

`EmailLoader` allows you to build dynamic templates for sending emails with Wordpress. It loads template files located in `[current_theme]/templates/emails/` under your theme's directory.

Two parameters can be sent to `EmailLoader::loadTemplate` :

* The __name__ of the template file (minus the .php extension).
* an optional array of __values__ used in the template

In the case of this typical call in a controller you will be able to format an email using a template located at `/templates/emails/my-notification.php` under your theme's directory.

~~~ php
<?php
namespace App\Controller;

use Strata\Emails\EmailLoader;

class ChildrenController extends AppController {

    protected function _sendEmail()
    {
        $values = array(
            "userId" => 10,
            "color" => "green"
        );
        $notification = EmailLoader::loadTemplate('my-notification', $values);
        $email = "test@domain.com";

        // Send confirmation emails to song admins and parent.
        EmailLoader::enableHTML();
        wp_mail($email, "This is the title of the email", $parentNotification);
        EmailLoader::disableHTML();
    }
}
?>
~~~

The template file named `my-notification.php` will then be able to access the variables sent through the `$values` array.

~~~ html
<ul>
    <li>The current user id is : <?php echo $userId; ?></li>
    <li>The current color is : <?php echo $color; ?></li>
</ul>
~~~
