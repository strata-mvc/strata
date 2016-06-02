<?php

namespace Strata\Model\Taxonomy;

use Strata\Model\WordpressEntity;
use Strata\Core\StrataObjectTrait;
use Strata\Model\CustomPostType\ModelEntity;
use Strata\Model\CustomPostType\QueriableEntityTrait;
use Strata\Strata;

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
     * Factories a model entity based on the Wordpress key
     * @param  string $str
     * @return mixed An instantiated model
     * @throws Exception
     */
    public static function factoryFromWpQuery()
    {
        global $wp_query;

        if ((bool)$wp_query->is_tax) {
            $term = get_term_by("slug", $wp_query->query_vars['term'], $wp_query->query_vars['taxonomy']);
            return ModelEntity::factoryFromString($term->taxonomy, $term);
        }

        if ($wp_query->queried_object && get_class($wp_query->queried_object) === "WP_Term") {
            $term = $wp_query->queried_object;
            return ModelEntity::factoryFromString($term->taxonomy, $term);
        }
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
        $wordpressKey = $this->getWordpressKey();
        $generatedQueryVar = Strata::config("runtime.taxonomy.query_vars.$wordpressKey");
        if (!is_null($generatedQueryVar)) {
            return $generatedQueryVar;
        }

        return $wordpressKey;
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
