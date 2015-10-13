<?php
namespace Strata\Model;

use Strata\Core\StrataObjectTrait;
use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\QueriableEntityTrait;
use Exception;

/**
 * Wraps User default objects.
 */
class User extends WordpressEntity
{
    use StrataObjectTrait;
    use QueriableEntityTrait;

    public $wpPrefix = "";
    public $permissionLevel = "edit_users";

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

    public function getQueryAdapter()
    {
        return new UserQuery();
    }
}
