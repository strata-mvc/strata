<?php
namespace Strata\Model\Taxonomy;

use Strata\Model\CustomPostType\QueriableEntity;

class TaxonomyEntity extends QueriableEntity
{
    public $wpPrefix = "tax_";

    public function getQueryAdapter()
    {
        return new TaxonomyQuery();
    }
}
