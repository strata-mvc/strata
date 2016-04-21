<?php

namespace Strata\Model\Taxonomy;

use Strata\Model\WordpressEntity;
use Strata\Core\StrataObjectTrait;
use Strata\Model\CustomPostType\ModelEntity;
use Strata\Model\CustomPostType\QueriableEntityTrait;

/**
 * A class that wraps around Wordpress' taxonomies.
 */
class Taxonomy extends WordpressEntity
{
    use StrataObjectTrait;
    use QueriableEntityTrait;

    /**
     * {@inheritdoc}
     */
    public static function getNamespaceStringInStrata()
    {
        return "Model\\Taxonomy";
    }

    /**
     * The Wordpress taxonomy identifier prefix
     * @var string
     */
    public $wpPrefix = "tax_";

    /**
     * The generated query_var key
     * @var straing
     */
    private $query_var = null;

    /**
     * Specifies whether Strata should attempt to automate routing
     * to the model's default controller when the custom post type's
     * slug is matched in the URL.
     * @var boolean|array
     */
    public $routed = false;

    /**
     * Returns the complete taxonomy query var variable
     */
    public function getQueryVar()
    {
        return $this->query_var;
    }

    /**
     * Sets the complete taxonomy query var variable
     */
    public function setQueryVar($var)
    {
        $this->query_var = $var;
    }

    /**
     * Return a TaxonomyQuery object on which requests
     * will be ran. Inheriting classes can modify this to suit their needs.
     * @return TaxonomyQuery
     */
    public function getQueryAdapter()
    {
        return new TaxonomyQuery();
    }

    /**
     * Informs the query to lookup post terms and not general taxonomies.
     * @param  ModelEntity $entity
     * @return TaxonomyQuery
     */
    public function forEntity(ModelEntity $entity)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->againstPostId((int)$entity->ID);
        return $this;
    }

    /**
     * Forces the query to enter get_term_by lookup mode.
     * @param  string $type  The field to lookup by
     * @param  mixed $value The expected value
     * @return TaxonomyQuery
     */
    public function by($type, $value)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->triggerLookupMode($type, $value);
        return $this;
    }
}
