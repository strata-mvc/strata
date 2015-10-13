<?php
namespace Strata\Model;

use Strata\Model\CustomPostType\Query;
use WP_User_Query;

class UserQuery extends Query
{
    protected $filters = array();

    public function fetch()
    {
        $this->logQueryStart();
        $query = new WP_User_Query($this->filters);
        $this->logQueryCompletion($this->toSql($query));
        return $query->results;
    }

    // WP_User_Query doesn't expose it's sql.
    private function toSql($query)
    {
        return "SELECT $query->query_fields $query->query_from $query->query_where $query->query_orderby $query->query_limit";
    }
}
