<?php
namespace Strata\Model;

use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\CustomPostType;
use Exception;

/**
 * Wraps Wordpress' default Post (of post_type 'post').
 */
class Post extends CustomPostType
{
    /**
     * Returns the current global value of get_post()
     * wrapped in a Strata entity.
     * @return CustomPostType
     */
    public static function getCurrentGlobal()
    {
        return Post::getEntity(get_post());
    }

    /**
     * The Wordpress custom post type identifier prefix
     * @var string
     */
    public $wpPrefix = "";

    /**
     * A list of taxonomies associated to the custom post type.
     * @var array
     */
    public $belongs_to = array("Strata\Model\Taxonomy\Category");

    /**
     * The permission level required for editing by the model
     * @var string
     */
    public $permissionLevel = 'edit_posts';

    /**
     * Returns a label object that exposes singular and plural labels
     * @return LabelParser
     */
    public function getLabel()
    {
        $labelParser = new LabelParser($this);
        $labelParser->parse();
        return $labelParser;
    }

    public function register()
    {
        throw new Exception("Posts cannot be registered.");
    }
}
