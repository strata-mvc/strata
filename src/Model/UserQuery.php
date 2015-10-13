<?php
namespace Strata\Model;

use Strata\Model\CustomPostType\Query;
use WP_User_Query;

class UserQuery extends Query
{
    public function fetch()
    {
        return (array)$this->query();
    }

    protected function executeFilteredQuery()
    {
        $this->logQueryStart();
        $query = new WP_User_Query($this->filters);
        $this->logQueryCompletion($this->toSql($query));
        return $query->results;
    }

    private function toSql($query)
    {
        // WP_User_Query doesn't expose it's sql.
        return "SELECT $query->query_fields $query->query_from $query->query_where $query->query_orderby $query->query_limit";
    }
}
