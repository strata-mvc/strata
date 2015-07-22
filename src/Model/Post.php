<?php
namespace Strata\Model;

use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\QueriableEntity;

/**
 * Wraps Post default objects.
 */
class Post extends QueriableEntity {

    public static function wordpressKey()
    {
        return "post";
    }

    public $configuration = array(
        "has" => array(
            "Strata\Model\Taxonomy\Category"
        )
    );

    /**
     * The permission level required for editing by the model
     * @var string
     */
    public $permissionLevel = 'edit_posts';

    /**
     * Returns the model's menu icon
     * @return string
     */
    public function getIcon()
    {
        return 'dashicons-admin-post';
    }

    /**
     * Returns the key Wordpress uses to identify this model.
     * @return string
     */
    public function getWordpressKey()
    {
        return Post::wordpressKey();
    }

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
}
