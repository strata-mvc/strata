---
layout: docs
title: Helpers
permalink: /docs/helpers/
---

## Creating a ViewHelper file.

To generate a `ViewHelper`, you should use the automated generator provided by WMVC. It will validate your object's name and ensure it will be correctly defined.

Using the command line, run the `generate` command from your project's base directory. In this exemple, we will generate a view helper for the `Artist` object:

~~~ sh
$ bin/mvc generate viewhelper Artist
~~~

## Use case

A `ViewHelper` is a class that helps view files by handling common and repetitive tasks. A common use case can be a class that wraps the logic of thumbnail presentation.

First, start by building your helper :

~~~ php
<?php
namespace MyProject\helper;

use MVC\View\Template;

/**
 * Renderer for post thumbnails
 */
class ThumbnailHelper extends \MVC\View\Helper\Helper
{
    /**
     * Renders the ACF Header
     * @param  integer
     * @return string
     */
    public function render($postID = null)
    {
        if (is_null($postID)) {
            $postID = get_the_ID();
        }

        $imgUrl = $this->_getImageUrl($postID);

        return Template::render(array(
            "imageurl"  => $imgUrl,
            "permalink" => post_permalink($postID),
            "alt"       => htmlspecialchars(get_the_title($postID))
        ));
    }

    /**
     * Return an image url for the current post
     * @param  integer
     * @return string
     */
    protected function _getImageUrl($postID)
    {
        if (has_post_thumbnail($postID)) {
            $thumbnailId = get_post_thumbnail_id($postID);
            $thumbnailObject = get_post($thumbnailId);
            return $thumbnailObject->guid;
        }

        return $this->_getDefaultThumbnailUrl();
    }

    protected function _getDefaultThumbnailUrl()
    {
        return sprintf("%s/assets/img/placeholder-medium.gif", get_template_directory_uri());
    }
}
?>
~~~

And then use it in your template files :

~~~ php
<?php
    $thumb   = new MyProject\Helper\ThumbnailHelper();
    echo $tumb->render();
?>
~~~

## Pre-packaged Helpers

Wordspress MVC currently only ships with the [FormHelper](/docs/helpers/formhelper/), from which you can derive to create your own forms.
