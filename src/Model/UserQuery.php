<?php
namespace Strata\Model;

use Strata\Model\CustomPostType\Query;
use WP_User_Query;

/**
 * The query is a cache of filters that will be eventually
 * sent to WP_User_Query. It allows chaining and object-oriented
 * manipulations of database queries.
 */
class UserQuery extends Query
{
    /**
     * Fetches the set matching the current state of the filters.
     * @return array An array of WP_Post objects
     */
    public function fetch()
    {
        return (array)$this->query();
    }

    /**
     * Executes the query based on the current filters.
     * @return mixed The result of the "new WP_User_Query()"
     */
    protected function executeFilteredQuery()
    {
        $this->logQueryStart();
        $query = new WP_User_Query($this->filters);
        $this->logQueryCompletion($this->toSql($query));
        return $query->results;
    }

    /**
     * Returns the SQL of the current query for logging
     * as WP_User_Query doesn't expose it's sql.
     * @param  WP_User_Query $query
     * @return string
     */
    private function toSql($query)
    {
        return "SELECT $query->query_fields $query->query_from $query->query_where $query->query_orderby $query->query_limit";
    }
}
