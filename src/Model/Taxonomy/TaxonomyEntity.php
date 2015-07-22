<?php
namespace Strata\Model\Taxonomy;

class TaxonomyEntity extends QueriableEntity
{
    public $wpPrefix = "tax_";

    /**
     * {@inheritdoc}
     */
    public function wordpressKey()
    {
        return "Taxonomy";
    }

    public function getQueryAdapter()
    {
        return new TaxonomyQuery();
    }
}
