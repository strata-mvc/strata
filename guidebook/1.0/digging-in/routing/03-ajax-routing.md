---
layout: guidebook
title: Ajax routing
permalink: /guidebook/1.0/digging-in/routing/ajax-routing/
menu_group: routing
---


Ajax in Wordpress can be difficult to achieve and Strata tailors a way to ease the process.

You may render content directly at the controller level and end the request before Wordpress' templating engine kicks in. Because there is no limit on the type of content you may render, controllers may apply a content type and various other of PHP's `header()` options during the request capture.

## Configuring the route

Assuming you want to call Wordpress' default url for ajax request. You would add the following routing path in `config/strata.php`:

{% highlight php linenos %}
<?php
    array('POST', '/wp/wp-admin/admin-ajax.php', 'AjaxController'),
?>
{% endhighlight %}

Notice here that no method has been entered as action to the `AjaxController` route. This is because Wordpress uses `$_POST['action']` to fork ajax requests and does not use distinct urls. Therefore Strata will call the method matching the value of the posted `$_POST['action']` value implicitly to determine the current action.

## Setting up the javascript

From anywhere within your active theme, you may set the ajax call as so :

{% highlight js linenos %}
<script>
    $.ajax({
        url: <?php echo admin_url('admin-ajax.php'); ?>,
        method: 'POST',
        data: {
            action: 'songs',
            security: wp_create_nonce('salt'),
            album_name: $('input[name=album_name]').val()
        }
    }).done(function(data){
       console.log(data);
    });
</script>
{% endhighlight %}

The important value here is the `action` parameter. This will automatically determine the intended controller action.

## Building the controller

The controller file could look like so :

{% highlight php linenos %}
<?php
namespace App\Controller;

use App\Model\Song;

class AjaxController extends AppController {

    public function songs()
    {
        $data = Song::repo()
            ->where("meta_key", "album_name")
            ->where("meta_value", $this->request->post('album_name']))
            ->first();

        $this->view->render(array(
            "Content-type" => "application/json",
            "content" => $data
        ));
    }
}
?>
{% endhighlight %}

In this case, this example will effectively return a JSON object representing the matched custom post type.
