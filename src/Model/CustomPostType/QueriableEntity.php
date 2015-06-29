<?php
namespace Strata\Model\CustomPostType;

use Strata\Model\WordpressEntity;
use Strata\Model\CustomPostType\Query;

class QueriableEntity extends WordpressEntity
{

    /**
     * Returns an instantiated object to access the repository.
     * @return QueriableEntity
     */
    public static function repo()
    {
        return self::staticFactory();
    }

    protected $activeQuery = null;

    /**
     * Return an object inheriting from Query on which requests
     * will be ran. Inheriting classes can modify this to suit their needs.
     * @return Strata\Model\CustomPostType\Query
     */
    public function getQueryAdapter()
    {
        return new Query();
    }

    public function resetCurrentQuery()
    {
        $this->activeQuery = null;
    }

    private function getCachedQueryAdapter()
    {
        if (is_null($this->activeQuery)) {
            $this->activeQuery = $this->getQueryAdapter();
        }

        return $this->activeQuery;
    }

    /**
     * Starts a wrapped wp_query pattern object. Used to chain parameters
     * It resets the query.
     * @return (Query) $query;
     */
    public function query()
    {
        $this->resetCurrentQuery();

        $query = $this->getCachedQueryAdapter();
        $query->type($this->getWordpressKey());

        return $this;
    }

    public function date($dateQuery)
    {
        $query = $this->getCachedQueryAdapter();
        $query->date($dateQuery);
        return $this;
    }

    public function orderby($orderBy)
    {
        $query = $this->getCachedQueryAdapter();
        $query->orderby($orderBy);
        return $this;
    }

    public function direction($order)
    {
        $query = $this->getCachedQueryAdapter();
        $query->direction($order);
        return $this;
    }

    public function status($status = null)
    {
        $query = $this->getCachedQueryAdapter();
        $query->status($status);
        return $this;
    }

    public function where($field, $value)
    {
        $query = $this->getCachedQueryAdapter();
        $query->where($field, $value);
        return $this;
    }

    public function limit($qty)
    {
        $query = $this->getCachedQueryAdapter();
        $query->limit($qty);
        return $this;
    }

    /**
     * Executes the query and resets it anew.
     * @return array posts
     */
    public function fetch()
    {
        $this->throwIfContextInvalid();

        $query = $this->getCachedQueryAdapter();
        $this->resetCurrentQuery();

        return $query->fetch();
    }

    /**
     * Executes the query and resets it anew.
     * @return hash of key => label values
     */
    public function listing($key, $label)
    {
        $this->throwIfContextInvalid();

        $query = $this->getCachedQueryAdapter();
        $this->resetCurrentQuery();

        return $query->listing($key, $label);
    }

    private function throwIfContextInvalid()
    {
        if (is_null($this->activeQuery)) {
            throw new Exception("No active query to fetch.");
        }
    }

    public function findAll()
    {
        return $this->query()->fetch();
    }

    public function findById($id)
    {
        return get_post($id);
    }

    public function count()
    {
        return wp_count_posts($this->getWordpressKey());
    }

}
