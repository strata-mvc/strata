<?php

namespace Strata\Model\Taxonomy;

use Strata\Model\CustomPostType\Query;
use Exception;

/**
 * The query is a cache of filters that will be eventually
 * sent to get_terms. It allows chaining and object-oriented
 * manipulations of database queries.
 */
class TaxonomyQuery extends Query
{
    /**
     * @var array A list of get_terms() filters.
     */
    protected $filters = array();

    /**
     * @var array A list of the taxonomies being queried.
     */
    protected $taxnomonies = array();

    /**
     * Sets the taxonomy type
     * @param  string $type
     * @return TaxonomyQuery
     */
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
