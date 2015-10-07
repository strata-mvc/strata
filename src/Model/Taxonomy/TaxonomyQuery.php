<?php
namespace Strata\Model\Taxonomy;

use Strata\Model\CustomPostType\Query;
use Exception;

class TaxonomyQuery extends Query
{

    protected $filters = array();
    protected $taxnomonies = array();

    public function type($type = null)
    {
        $this->taxnomonies[] = $type;
        return $this;
    }

    /**
     * Adds a taxonomy to filter terms from
     * @param  string $taxonomy
     * @return TaxonomyQuery
     */
    public function in($taxonomy)
    {
        return $this->type($taxonomy->wordpressKey());
    }

    /**
     * Fetches the terms matching the TaxonomyQuery.
     * @return array
     */
    public function fetch()
    {
        return get_terms($this->taxnomonies, $this->filters);
    }
}
