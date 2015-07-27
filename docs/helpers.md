---
layout: docs
title: Helpers
permalink: /docs/helpers/
---

## Creating a ViewHelper file.

To generate a `ViewHelper`, you should use the automated generator provided by Strata. It will validate your object's name and ensure it will be correctly defined.

Using the command line, run the `generate` command from your project's base directory. In this example, we will generate a view helper for the `Artist` object:

~~~ sh
$ ./strata generate helper Artist
~~~

## Use case

A `ViewHelper` is a class that helps view files by handling common and repetitive tasks. A common use case can be a class that wraps the logic of thumbnail presentation.

First, start by building your helper :

~~~ php
<?php
namespace App\View\Helper;

use Strata\View\Template;

/**
 * Renderer for post thumbnails
 */
class ThumbnailHelper extends AppHelper
{
    private $config = null;

    function __construct($config = array())
    {
        $this->config = $config;
    }

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

        $imgUrl = $this->getImageUrl($postID);

        return Template::parse("partials/thumbnail", array(
            "imageUrl"  => $imgUrl,
            "permalink" => post_permalink($postID),
            "alt"       => htmlspecialchars(get_the_title($postID))
        ));
    }

    /**
     * Return an image url for the current post
     * @param  integer
     * @return string
     */
    protected function getImageUrl($postID)
    {
        if (has_post_thumbnail($postID)) {
            $thumbnailId = get_post_thumbnail_id($postID);
            $thumbnailObject = get_post($thumbnailId);
            return $thumbnailObject->guid;
        }

        return $this->getDefaultThumbnailUrl();
    }

    protected function getDefaultThumbnailUrl()
    {
        return sprintf("%s/assets/img/placeholder-medium.gif", get_template_directory_uri());
    }
}
?>
~~~

Once your helper is ready, you must include it from within your controllers. You can have them be autoloaded by adding them to the public `$helpers` attribute, or manually using `$this->view->loadHelper()`.

Both of these methods allow for a configuration array that can be sent to the helper's constructor. You can send values that you will handle yourself afterwards within the helper. The only value Strata will look for is the `name` key. If it is sent as part of the configuration, the helper will be declared with this variable name in the view files. Otherwise the variable name in the view is always suffixed with `Helper`.

~~~ php
<?php
namespace App\Controller;

use App\Model\Page;

class ArtistController extends AppController {

    public $helpers = array(
        "Thumbnail",
        "Acf" => array(
            "name" => "Acf"
        )
    );

    public function init()
    {
        parent::init();

        // OR you can declare them manually
        if (get_the_ID() == Page::schedulerPageID()) {
            $this->view->loadHelper("Calendar", array("numberOfDays" => 5));
        }
    }

    public function index()
    {

    }

}
?>
~~~

Once the helpers are planned to automatically be instantiated by your controllers, you can use them in the template files like so:

~~~ php
<h1><?php the_title(); ?></h1>

<?php echo $ThumbnailHelper->render(); ?>

<article><?php the_content(); ?></article>

~~~

## Pre-packaged Helpers

Strata currently ships with the [FormHelper](/docs/helpers/formhelper/), from which you can derive to create your own forms.
