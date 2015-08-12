<?php
namespace Strata\Model;

use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\Entity;

/**
 * Wraps Post default objects.
 */
class Post extends Entity {

    public $routed = false;
    public $wpPrefix = "";
    public $belongs_to = array("Strata\Model\Taxonomy\Category");
    public $configuration = array();

    /**
     * The permission level required for editing by the model
     * @var string
     */
    public $permissionLevel = 'edit_posts';

    function __construct()
    {
        $this->admin_menus = Hash::normalize($this->admin_menus);
        $this->belongs_to = Hash::normalize($this->belongs_to);
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
