<?php
namespace Strata\Model;

use Strata\Model\CustomPostType\Query;
use WP_User_Query;

class UserQuery extends Query
{

    protected $filters = array();

    public function fetch()
    {
        $query = new WP_User_Query($this->filters);
        return $query->results;
    }
}
