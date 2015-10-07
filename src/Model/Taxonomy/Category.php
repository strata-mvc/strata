<?php
namespace Strata\Model\Taxonomy;

/**
 *  Placeholder for post categories
 */
class Category extends TaxonomyEntity
{

    /**
     * {@inheritdoc}
     */
    public function wordpressKey()
    {
        return "category";
    }
}
