<?php

namespace Strata\Model\CustomPostType;

use Strata\Model\CustomPostType\Query;
use Exception;

/**
 * The QueriableEntity trait allows for more object-oriented database queries
 * by adding a layer of abstraction with WP_Query.
 * @link https://codex.wordpress.org/Class_Reference/WP_Query
 */
trait QueriableEntityTrait
{
    /**
     * Returns an instantiated QueriableEntity to access the repository.
     * @return QueriableEntity
     */
    public static function repo()
    {
        $ref = self::staticFactory();
        return $ref->query();
    }

    /**
     * Saves a pointer to the currently active query adapter.
     * @var Query
     */
    protected $activeQuery = null;

    /**
     * Return an object inheriting from Query on which requests
     * will be ran. Inheriting classes can modify this to suit their needs.
     * @return Query
     */
    public function getQueryAdapter()
    {
        return new Query();
    }

    /**
     * Resets (forgets) the active query.
     */
    public function resetCurrentQuery()
    {
        $this->activeQuery = null;
    }

    /**
     * Starts a wrapped wp_query pattern object. Used to chain parameters
     * It resets the query.
     * @return QueriableEntity;
     */
    public function query()
    {
        $this->resetCurrentQuery();
        $this->reloadQueryAdapter();
        return $this;
    }

    /**
     * Applies a date query to the querier.
     * @link https://codex.wordpress.org/Class_Reference/WP_Query#Date_Parameters
     * @param  array $dateQuery An array of the date query parameters for WP_Query
     * @return QueriableEntity
     */
    public function date($dateQuery)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->date($dateQuery);
        return $this;
    }

    /**
     * Defines fields on which to order the current query.
     * @param  string $orderBy
     * @return QueriableEntity
     */
    public function orderby($orderBy)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->orderby($orderBy);
        return $this;
    }

    /**
     * Defines the direction on which the orderby() field
     * will be sorted.
     * @param  string $dir
     * @return QueriableEntity
     */
    public function direction($dir)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->direction($dir);
        return $this;
    }

    /**
     * Shorthand for setting conditions on the post_status
     * column.
     * @param  string $status
     * @return QueriableEntity
     */
    public function status($status = null)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->status($status);
        return $this;
    }

    /**
     * The default catch all wrapper for WP_Query parameters.
     * @param  string $field
     * @param  mixed $value
     * @return QueriableEntity
     */
    public function where($field, $value)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->where($field, $value);
        return $this;
    }

    /**
     * Allows the branching of different conditions in the same
     * Wordpress query.
     * @param  string $field
     * @param  mixed $value
     * @return QueriableEntity
     */
    public function orWhere($field, $value)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->orWhere($field, $value);
        return $this;
    }

    /**
     * Sets the maximum number of results the current query may
     * return.
     * @param  integer $qty
     * @return QueriableEntity
     */
    public function limit($qty)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->limit($qty);
        return $this;
    }

    /**
     * Sets the starting offset of the current queries result set.
     * @param  index $index
     * @return QueriableEntity
     */
    public function offset($index)
    {
        $this->reloadQueryAdapter();
        $this->activeQuery->offset($index);
        return $this;
    }

    /**
     * Adds a 'author' condition to the current query.
     * @param  integer $authorID
     * @return QueriableEntity
     */
    public function byAuthorID($authorID)
    {
        return $this->where('author', $authorID);
    }

    public function byName()
    {
        return $this
            ->orderby("post_title")
            ->direction("ASC");
    }

    public function published()
    {
        return $this->status("publish");
    }

    public function byRecency()
    {
        return $this
            ->orderby("creation_date")
            ->direction("DESC");
    }

    public function byMenuOrder()
    {
        return $this
            ->orderby("menu_order")
            ->direction("ASC");
    }

    /**
     * Fetches the number of results the current queries returned.
     * This ends the current query.
     * @return integer
     */
    public function count()
    {
        $this->throwIfContextInvalid();

        $this->reloadQueryAdapter();
        $results = $this->activeQuery->fetch();

        $this->resetCurrentQuery();

        return count($results);
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

        if (!is_array($results) && get_class($results) === "WP_Error") {
            throw new Exception(json_encode($results));
        }

        return $this->wrapInEntities($results);
    }

    /**
     * Executes the query and resets it anew.
     * This is useful for returning a list of
     * [ID => post_title] elements.
     * @return hash of $key => $label values
     */
    public function listing($key, $label)
    {
        $this->throwIfContextInvalid();

        $this->reloadQueryAdapter();
        $results = $this->activeQuery->listing($key, $label);

        $this->resetCurrentQuery();

        return $results;
    }

    /**
     * Fetches the first element matching the current query and
     * resets it anew.
     * @return Strata\Model\CustomPostType\ModelEntity
     */
    public function first()
    {
        $result = $this->limit(1)->fetch();
        return array_shift($result);
    }

    /**
     * Returns all the possible entities and ends the query.
     * @return array
     */
    public function findAll()
    {
        return $this->query()->fetch();
    }

    /**
     * Finds a model entity by it's ID.
     * @param  integer $id
     * @return Strata\Model\CustomPostType\ModelEntity
     */
    public function findById($id)
    {
        $post = get_post($id);
        if (!is_null($post)) {
            return self::getEntity($post);
        }
    }

    /**
     * Returns the total number of entities of the current type.
     * Unlike count() this does not take additional filtering
     * and maps to wp_count_posts().
     * @return integer
     */
    public function countTotal()
    {
        return wp_count_posts($this->getWordpressKey());
    }

    /**
     * Paginates the current query. Must be called before
     * the result set has been generated.
     * Maps to paginate_links()
     * @param  array  $config A configuration array that will be sent to paginate_links()
     * @return string A pagination HTML block
     */
    public function paginate($config = array())
    {
        $this->reloadQueryAdapter();
        return $this->activeQuery->paginate($config);
    }

    /**
     * Wraps the resultset into entities of the current object type.
     * @param  array  $entities
     * @return array
     */
    protected function wrapInEntities(array $entities)
    {
        $results = array();
        foreach ($entities as $entity) {
            $results[] = self::getEntity($entity);
        }

        return $results;
    }

    /**
     * Reloads the query adapter when it has not been initialized.
     * Should none be set, it instantiates it with the correct default values.
     * @return Query
     */
    private function reloadQueryAdapter()
    {
        if (is_null($this->activeQuery)) {
            $this->activeQuery = $this->getQueryAdapter();
            $this->activeQuery->type($this->getWordpressKey());
        }

        return $this->activeQuery;
    }

    /**
     * Throws an exception when the query is manipulated
     * after a resultset has been generated.
     * @throws Exception
     */
    private function throwIfContextInvalid()
    {
        if (is_null($this->activeQuery)) {
            throw new Exception("No active query to fetch.");
        }
    }
}
