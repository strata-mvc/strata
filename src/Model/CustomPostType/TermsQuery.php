<?php
namespace MVC\Model\CustomPostType;

class TermsQuery
{
    // Set defaults: return a list of published posts ordered by name
    protected $_filters = array(
        'orderby'           => 'name',
        'order'             => 'ASC',
        'hide_empty'        => true,
        'exclude'           => array(),
        'exclude_tree'      => array(),
        'include'           => array(),
        'number'            => '',
        'fields'            => 'all',
        'slug'              => '',
        'parent'            => '',
        'hierarchical'      => true,
        'child_of'          => 0,
        'get'               => '',
        'name__like'        => '',
        'description__like' => '',
        'pad_counts'        => false,
        'offset'            => '',
        'search'            => '',
        'cache_domain'      => 'core'
    );
    protected $_taxonomies = array();

    public function taxonomy($taxonomy)
    {
        $this->_taxonomies[] = $taxonomy;
        return $this;
    }

    public function fetch()
    {
        return get_terms($this->_taxonomies, $this->_filters);
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

    public function slug($slug)
    {
        $this->_filters['slug'] = $slug;
        return $this;
    }

    public function where($field, $value)
    {
        $this->_filters[$field]   = $value;
        return $this;
    }

}
