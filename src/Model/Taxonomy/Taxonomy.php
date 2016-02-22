<?php

namespace Strata\Model\Taxonomy;

use Strata\Model\WordpressEntity;
use Strata\Core\StrataObjectTrait;
use Strata\Model\CustomPostType\QueriableEntityTrait;


/**
 * A class that wraps around Wordpress' taxonomies.
 */
class Taxonomy extends WordpressEntity
{
    use StrataObjectTrait;
    use QueriableEntityTrait;

    /**
     * The Wordpress taxonomy identifier prefix
     * @var string
     */
    public $wpPrefix = "tax_";

    /**
     * Return a TaxonomyQuery object on which requests
     * will be ran. Inheriting classes can modify this to suit their needs.
     * @return TaxonomyQuery
     */
    public function getQueryAdapter()
    {
        return new TaxonomyQuery();
    }
}
