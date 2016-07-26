---
layout: guidebook
title: Localization
permalink: /guidebook/1.0/getting-started/creating-projects/localization/
covered_tags: configuration, localization
menu_group: creating-projects
---

Strata objects can be localized across the whole spectrum of what it handles. To do so, Strata will use the `i18n.locales` configuration key of the global configuration object.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php

$strata = array(
    "i18n" => array(
        "textdomain" => "my_application",
        "locales" => array(
            "en_CA" => array("nativeLabel" => "English", "url" => "en", "default" => true),
            "fr_CA" => array("nativeLabel" => "Français", "url" => "fr"),
            "es_ES" => array("nativeLabel" => "Español", "url" => "es"),
        )
    ),
);

return $strata;
?>
{% endhighlight %}
{% include terminal_end.html %}

A unique `textdomain` for your application must be set. It will be the secondary parameter to `__()` within your application's files.

{% include terminal_start.html %}
{% highlight php linenos %}
<p><?php _e("Hello!", "my_application"); ?></p>
{% endhighlight %}
{% include terminal_end.html %}


### Locale configuration

A locale configuration will be associated to the ISO language code and will supply the `nativeLabel`, it's slug prefix as `url` and whether or not it should be used as default language with `default`.

Additionally, you may supply additional fields which will not be used by Strata but can be useful in the context of your application.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
$strata = array(
    "i18n" => array(
        "locales" => array(
            "fr_CA" => array(
                "nativeLabel" => "Français",
                "url" => "fr",
                "region" => "North America",
                "country" => "Canada",
                "province" => "Quebec",
            ),
        )
    ),
);
?>
{% endhighlight %}
{% include terminal_end.html %}


Required configuration keys should be accessed through [predefined methods](/api/1.0/classes/Strata_I18n_Locale.html) while custom configuration may be accessed through a `Locale` object like so :

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
    use Strata\Strata;

    $locale = Strata::i18n()->getCurrentLocale();
    debug(array(
        $locale->getConfig("region"),
        $locale->getConfig("country"),
        $locale->getConfig("province"),
    ));
?>
{% endhighlight %}
{% include terminal_end.html %}
