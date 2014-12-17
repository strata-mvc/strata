<?php
namespace MVC\CustomPostTypes;

class Query
{
    // Set defaults: return a list of published posts ordered by name
    protected $_filters = array(
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_status'      => 'publish',
        'nopaging'         => true,
        'suppress_filters' => true,
    );

    public function fetch()
    {
        return get_posts($this->_filters);
    }

    public function listing($key, $label)
    {
        $data = array();
        foreach ($this->fetch() as $entity) {
            $data[$entity->{$key}] = $entity->{$label};
        }
        return $data;
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

    public function type($type)
    {
        $this->_filters['post_type'] = $type;
        return $this;
    }

    public function status($status)
    {
        $this->_filters['post_status'] = $status;
        return $this;
    }

    public function join($type, $value)
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

}
