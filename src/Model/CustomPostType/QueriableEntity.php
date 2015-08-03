<?php
namespace Strata\Model\CustomPostType;

use Strata\Model\WordpressEntity;
use Strata\Model\CustomPostType\Query;
use Strata\Model\CustomPostType\ModelEntity;

class QueriableEntity extends WordpressEntity
{

    /**
     * Returns an instantiated object to access the repository.
     * @return QueriableEntity
     */
    public static function repo()
    {
        $ref = self::staticFactory();
        return $ref->query();
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

    private function reloadQueryAdapter()
    {
        if (is_null($this->activeQuery)) {
            $this->activeQuery = $this->getQueryAdapter();
            $this->activeQuery->type($this->getWordpressKey());
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
        $this->reloadQueryAdapter();
        return $this;
    }

    public function date($dateQuery)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->date($dateQuery);
        return $this;
    }

    public function orderby($orderBy)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->orderby($orderBy);
        return $this;
    }

    public function direction($order)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->direction($order);
        return $this;
    }

    public function status($status = null)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->status($status);
        return $this;
    }

    public function where($field, $value)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->where($field, $value);
        return $this;
    }

    public function orWhere($field, $value)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->orWhere($field, $value);
        return $this;
    }

    public function limit($qty)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->limit($qty);
        return $this;
    }

    /**
     * Executes the query and resets it anew.
     * @return array posts
     */
    public function fetch()
    {
        $this->throwIfContextInvalid();

        $this->reloadQueryAdapter();
        $results = $this->activeQuery->fetch();

        $this->resetCurrentQuery();

        return $this->wrapInEntities($results);

        return $results;
    }

    /**
     * Executes the query and resets it anew.
     * @return hash of key => label values
     */
    public function listing($key, $label)
    {
        $this->throwIfContextInvalid();

        $this->reloadQueryAdapter();
        $results = $this->activeQuery->listing($key, $label);

        $this->resetCurrentQuery();

        return $results;
    }

    public function first()
    {
        $result = $this->fetch();
        return array_shift($result);
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
        $EntityClass = $this->getEntityWrapperClass();
        $post = get_post($id);

        if (!is_null($post)) {
            return new $EntityClass($post);
        }
    }

    public function count()
    {
        return wp_count_posts($this->getWordpressKey());
    }

    private function wrapInEntities(array $entities)
    {
        $EntityClass = $this->getEntityWrapperClass();
        $results = array();
        foreach ($entities as $entity) {
            $results[] = new $EntityClass($entity);
        }

        return $results;
    }

    private function getEntityWrapperClass()
    {
        $original = ModelEntity::generateClassPath(get_called_class());
        return class_exists($original) ? $original : "\\Strata\\Model\\CustomPostType\\ModelEntity";
    }

}
