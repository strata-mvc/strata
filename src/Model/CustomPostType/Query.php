<?php
namespace Strata\Model\CustomPostType;

class Query
{
    // Set defaults: return a list of published posts ordered by name
    protected $_filters = array(
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_status'      => 'any',
        'nopaging'         => true,
        'suppress_filters' => true,
    );

    public function fetch()
    {
        $query = $this->query();
        return $query->posts;
    }

    public function query()
    {
        return new \WP_Query($this->_filters);
    }

    public function listing($key, $label)
    {
        $data = array();
        foreach ($this->fetch() as $entity) {
            $data[$entity->{$key}] = $entity->{$label};
        }
        return $data;
    }

    public function date($dateQuery)
    {
        $this->_filters['date_query'] = $dateQuery;
        return $this;
    }

    public function orderby($orderBy)
    {
        $this->_filters['orderby'] = $orderBy;
        return $this;
    }

    public function direction($order)
    {
        $this->_filters['order'] = $order;
        return $this;
    }

    public function type($type = null)
    {
        if (is_null($type)) {
            unset($this->_filters['post_type']);
        } else {
            $this->_filters['post_type'] = $type;
        }
        return $this;
    }

    public function status($status = null)
    {
        if (is_null($status)) {
            unset($this->_filters['post_status']);
        } else {
            $this->_filters['post_status'] = $status;
        }

        return $this;
    }

    /** This is deprecated as its not really a join query. */
    public function join($type, $value = null)
    {
        $this->_filters['meta_key']   = $type;
        $this->_filters['meta_value'] = intval($value);
        return $this;
    }

    public function where($field, $value)
    {
        $this->_filters[$field]   = $value;
        return $this;
    }

    public function limit($qty)
    {
        $this->_filters['posts_per_page']   = $qty;
        $this->_filters['nopaging']         = false;
        return $this;
    }

}
