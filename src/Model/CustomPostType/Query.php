<?php

namespace Strata\Model\CustomPostType;

use Strata\Strata;
use WP_Query;

/**
 * The query is a cache of filters that will be eventually
 * sent to WP_Query. It allows chaining and object-oriented
 * manipulations of database queries.
 */
class Query
{
    /**
     * A list of WP_Query filters. Defaults to a list of published posts
     * ordered by name alphabetically unpaged.
     * @var array
     */
    protected $filters = array(
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_status'      => 'any',
        'nopaging'         => true,
        'suppress_filters' => true,

        // This field, though not used by Wordpress, will
        // allow some flexibility with combining AND and OR query
        // relations.
        'strata_relations' => array(
            'meta_query' => array("AND" => array(), "OR" => array()),
            'tax_query' => array("AND" => array(), "OR" => array()),
        ),
    );

    /**
     * @var integer Query timer
     */
    private $executionStart = 0;

    /**
     * Fetches the set matching the current state of the filters.
     * @return array An array of WP_Post objects
     */
    public function fetch()
    {
        $query = $this->query();
        return $query->posts;
    }

    /**
     * Executes the query using the current filters.
     * @return array
     */
    public function query()
    {
        $this->carryOverIncompatibleQueries();
        $this->relationsToQueries();
        return $this->executeFilteredQuery();
    }

    /**
     * Returns a custom listing based on the search
     * results.
     * @param  string $key
     * @param  string $label
     * @return array
     */
    public function listing($key, $label)
    {
        $data = array();
        foreach ($this->fetch() as $entity) {
            $data[$entity->{$key}] = $entity->{$label};
        }
        return $data;
    }

    /**
     * Adds a 'data_query' condition
     * @param  array $dateQuery
     * @return Query
     */
    public function date($dateQuery)
    {
        $this->filters['date_query'] = $dateQuery;
        return $this;
    }

    /**
     * Adds an 'orderby' condition.
     * @param  string $orderBy
     * @return Query
     */
    public function orderby($orderBy)
    {
        $this->filters['orderby'] = $orderBy;
        return $this;
    }

    /**
     * Adds an 'order' condition
     * @param  string $order
     * @return Query
     */
    public function direction($order)
    {
        $this->filters['order'] = $order;
        return $this;
    }

    /**
     * Adds a 'post_type' condition
     * @param  string $type
     * @return Query
     */
    public function type($type = null)
    {
        if (is_null($type)) {
            unset($this->filters['post_type']);
        } else {
            $this->filters['post_type'] = $type;
        }
        return $this;
    }

    /**
     * Adds a 'post_status' condition
     * @param  string $status
     * @return Query
     */
    public function status($status = null)
    {
        if (is_null($status)) {
            unset($this->filters['post_status']);
        } else {
            $this->filters['post_status'] = $status;
        }

        return $this;
    }

    /**
     * Adds any type of condition.
     * @param  string $field
     * @param  string $value
     * @return Query
     */
    public function where($field, $value)
    {
        if (strtolower($field) === "meta_query") {
            return $this->metaWhere($field, $value);
        } elseif (strtolower($field) === "tax_query") {
            return $this->taxWhere($field, $value);
        }

        $this->filters[$field] = $value;
        return $this;
    }

    /**
     * Allows for branching of query conditions.
     * @param  string $field
     * @param  string $value
     * @return Query
     */
    public function orWhere($field, $value)
    {
        if (strtolower($field) === "meta_query") {
            return $this->metaWhere($field, $value, 'OR');
        } elseif (strtolower($field) === "tax_query") {
            return $this->taxWhere($field, $value, 'OR');
        }

        $this->filters[$field] = $value;
        return $this;
    }

    /**
     * Paginates the current query using paginate_links()
     * @param  array  $config
     * @return string
     */
    public function paginate($config = array())
    {
        $query = $this->query();
        $count = $query->post_count;
        $postsPerPage = $query->query_vars['posts_per_page'];
        $offset = (int)get_query_var('paged', 1);

        $this->limit($postsPerPage);

        if ($offset > 1) {
            $this->offset(($offset-1) * $postsPerPage);
        }

        if ($count > $postsPerPage) {
            $config +=  array(
                'mid-size' => 1,
                'current' => $offset === 0 ? 1 : $offset,
                'total' => ceil($count / $postsPerPage),
                'prev_next' => true,
                'prev_text' => __('Previous', 'strata'),
                'next_text' => __('Next', 'strata')
            );

            return paginate_links($config);
        }

        return "";
    }

    /**
     * Sets the 'post_per_page' condition on the current query.
     * @param  integer $qty
     * @return Query
     */
    public function limit($qty)
    {
        $this->filters['posts_per_page']   = $qty;
        $this->filters['nopaging']         = false;
        return $this;
    }

    /**
     * Sets the 'offset' condition on the current query.
     * @param  integer $idx
     * @return Query
     */
    public function offset($idx)
    {
        $this->filters['offset']   = $idx;
        return $this;
    }

    /**
     * Gets the list of active filters.
     * @return array
     */
    public function getFilters()
    {
        return (array)$this->filters;
    }

    /**
     * Applies multiple filters to the current query
     * in one pass.
     * @param  array $filters
     * @return Query
     */
    public function applyFilters($filters)
    {
        foreach ($filters as $key => $value) {
            $this->filters[$key] = $value;
        }

        return $this;
    }

    /**
     * Executes the query based on the current filters.
     * @return mixed The result of the "new WP_Query()"
     */
    protected function executeFilteredQuery()
    {
        $this->logQueryStart();
        $result = new WP_Query($this->getFilters());
        $this->logQueryCompletion($result->request);
        return $result;
    }

    /**
     * If previous meta queries are set, they may prevent the combination of
     * both 'AND' and 'OR' relation. Carries over the exclusive fields as a list of
     * IDs to apply in a Query with a new meta_query relation type.
     * @return Query
     */
    protected function carryOverIncompatibleQueries()
    {
        foreach ($this->filters['strata_relations'] as $queryType => $queryDetails) {
            if ($this->hasRelationQuery($queryType, 'AND') && $this->hasRelationQuery($queryType, 'OR')) {
                $this->andRelationToPostIn($queryType);
            }
        }

        return $this;
    }

    /**
     * Interprets the branching queries into resultsets that will be
     * applied to the main query
     * @return Query
     */
    protected function relationsToQueries()
    {
        foreach ($this->filters['strata_relations'] as $queryType => $queryDetails) {
            // At this point, there should only be exclusive AND or OR query groups
            $metaQueries = null;
            $relationTypes = array_keys($queryDetails);
            foreach ($relationTypes as $relationType) {
                if ($this->hasRelationQuery($queryType, $relationType)) {
                    $metaQueries = $this->getRelationQuery($queryType, $relationType);
                    $this->setQueryRelation($queryType, $relationType);
                    $this->resetQueryRelation($queryType, $relationType);
                }
            }

            if (!is_null($metaQueries)) {
                foreach ($metaQueries as $query) {
                    $this->addRelationQuery($queryType, $query);
                }
            }
        }

        return $this;
    }

    /**
     * Takes in a resultsets and maps it to a 'post__in'
     * condition to port the results to the main query.
     * @param  string $query_type
     * @return Query
     */
    protected function andRelationToPostIn($query_type)
    {
        $andQuery = new Query();

        // Copy the current Query but remove the OR conditions.
        // They will be looked up as this instance goes on with
        // the process.
        $andQuery->applyFilters($this->filters);
        $andQuery->resetQueryRelation($query_type, 'OR');

        // This forces the AND relationships to be loaded before
        // comparing the or parameters
        $andIds = $andQuery->listing("ID", "ID");
        $this->where('post__in', array_values($andIds));
        $this->resetQueryRelation($query_type, 'AND');

        return $this;
    }

    /**
     * Removes the query relationships that matter only to Strata.
     * @param  string $which
     * @param  string $type
     * @return Query
     */
    public function resetQueryRelation($which, $type)
    {
        $this->filters['strata_relations'][$which][$type] = array();
        return $this;
    }

    /**
     * Fetches a special relation query
     * @param  string $which
     * @param  string $type
     * @return Query
     */
    public function getRelationQuery($which, $type)
    {
        return $this->filters['strata_relations'][$which][$type];
    }

    /**
     * Confirms if there is a special filter for a relation query.
     * @param  string  $which
     * @param  straing  $type
     * @return boolean
     */
    public function hasRelationQuery($which, $type)
    {
        return count($this->getRelationQuery($which, $type)) > 0;
    }

    /**
     * Sets a new relationship query.
     * @param string $type
     * @param string $which
     * @return Query
     */
    public function setQueryRelation($type, $which)
    {
        $this->prepareRelationFilter($type);
        $this->filters[$type]['relation'] = $which;
        return $this;
    }

    /**
     * Prepopulates a special relation query
     * @param  string $type
     * @return Query
     */
    public function prepareRelationFilter($type)
    {
        if (!array_key_exists($type, $this->filters) || !is_array($this->filters[$type])) {
            $this->filters[$type] = array();
        }
        return $this;
    }

    /**
     * This does not actually set the meta_query parameter. It is
     * used to build a more complex AND/OR logical fetch.
     * @return Query
     */
    protected function metaWhere($field, $value, $compare = 'AND')
    {
        $this->filters['strata_relations']['meta_query'][$compare][] = $value;
        return $this;
    }

    /**
     * This does not actually set the meta_query parameter. It is
     * used to build a more complex AND/OR logical fetch.
     * @return Query
     */
    protected function taxWhere($field, $value, $compare = 'AND')
    {
        $this->filters['strata_relations']['tax_query'][$compare][] = $value;
        return $this;
    }

    /**
     * Adds a new relation filter
     * @param string $type
     * @param string $value
     * @return Query
     */
    private function addRelationQuery($type, $value)
    {
        $this->prepareRelationFilter($type);
        $this->filters[$type][] = $value;
        return $this;
    }

    /**
     * Sets the beginning microtime of when the query has begun reading the
     * database.
     */
    protected function logQueryStart()
    {
        $this->executionStart = microtime(true);
    }

    /**
     * Logs the completion time of the database query.
     * @param  string $sql
     */
    protected function logQueryCompletion($sql)
    {
        $executionTime = microtime(true) - $this->executionStart;
        $timer = sprintf(" (Done in %s seconds)", round($executionTime, 4));

        $oneLine = preg_replace('/\s+/', ' ', trim($sql));
        $app = Strata::app();
        $app->log($oneLine . $timer, "[Strata:Query]");
    }
}
