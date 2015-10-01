<?php
namespace Strata\Model\Taxonomy;

use Strata\Model\WordpressEntity;
use Strata\Core\StrataObjectTrait;
use Strata\Model\CustomPostType\QueriableEntityTrait;

class TaxonomyEntity extends WordpressEntity
{
    use StrataObjectTrait;
    use QueriableEntityTrait;

    public $wpPrefix = "tax_";

    public function __construct()
    {}

    public function getQueryAdapter()
    {
        return new TaxonomyQuery();
    }
}
