---
layout: guidebook
title: Custom Post Type Localization
permalink: /guidebook/1.0/digging-in/models/localization/
covered_tags: models, custom-post-types, url-rewrite, localization
menu_group: models
---

Both the Custom Post Type slug and the additional sub-URLs can be localized at runtime to the active locale. The configuration remains similar but for the extra `i18n` configuration keys.

## Slugs

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model;

class MyCPT extends AppCustomPostType
{
    public $configuration = array(
        "rewrite"   => array(
            'slug'       => 'business/industry',
        ),
        'i18n' => array(
            'fr_CA' => array(
                "rewrite"   => array('slug' => 'affaires/industrie'),
            ),
            'es_ES' => array(
                "rewrite"   => array('slug' => 'negocios/industria'),
            ),
        ),
    );
}
?>
{% endhighlight %}
{% include terminal_end.html %}

## Additional urls

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model;

class MyCPT extends AppCustomPostType
{
     public $routed = array(
        "rewrite" =>  array(
            'send_contact' => 'send',
        ),
        'i18n' => array(
            'fr_CA' => array(
                "rewrite" => array(
                    'send_contact' => 'envoyer',
                ),
            ),
            'es_ES' => array(
                "rewrite" => array(
                    'send_contact' => 'enviar',
                ),
            ),
        ),
    );
}
?>
{% endhighlight %}
{% include terminal_end.html %}
