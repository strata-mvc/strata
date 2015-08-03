<?php
namespace Strata\Model\Taxonomy;

use Strata\Model\CustomPostType\Query;
use Exception;

class TaxonomyQuery extends Query {

    protected $_filters = array();
    protected $_taxnomonies = array();

    /**
     * Adds a taxonomy to filter terms from
     * @param  string $taxonomy
     * @return TaxonomyQuery
     */
    public function in($taxonomy)
    {
        $this->_taxnomonies[] = $taxonomy->wordpressKey();
        return $this;
    }

    /**
     * Fetches the terms matching the TaxonomyQuery.
     * @return array
     */
    public function fetch()
    {
        return get_terms($this->_taxnomonies, $this->_filters);
    }
}