<?php
namespace Strata\Model\Taxonomy;

use Strata\Utility\Hash;

use Strata\Model\WordpressEntity;
use Strata\Model\CustomPostType\Registrar\TaxonomyRegistrar;

use Strata\Model\Model;

class TaxonomyEntity extends WordpressEntity
{
    public $wpPrefix = "tax_";
}
