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

    /**
     * The Wordpress custom post type identifier prefix
     * @var string
     */
    public $wpPrefix = "";

    /**
     * The permission level required for editing by the model
     * @var string
     */
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

    /**
     * Return an object inheriting from Query on which requests
     * will be ran. Inheriting classes can modify this to suit their needs.
     * @return UserQuery
     */
    public function getQueryAdapter()
    {
        return new UserQuery();
    }

    /**
     * Wraps the resultset into entities of the current object type.
     * @param  array  $entities
     * @return array
     */
    protected function wrapInEntities(array $entities)
    {
        $results = array();
        foreach ($entities as $entity) {
            $results[] = self::getEntity($entity->data);
        }

        return $results;
    }
}
