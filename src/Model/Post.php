<?php
namespace Strata\Model;

use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\Entity;

/**
 * Wraps Post default objects.
 */
class Post extends Entity {

    public $wpPrefix = "";
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
}
