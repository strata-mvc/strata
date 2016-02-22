<?php

namespace Strata\Model\CustomPostType;

use Strata\Utility\Inflector;

/**
 * Based on model configuration and the model class name,
 * generates a list of Wordpress labels automatically.
 */
class LabelParser
{
    /**
     * @var string The plural definition
     */
    private $plural = "";

    /**
     * @var string The singular
     */
    private $singular = "";


    /**
     * @var mixed A class
     */
    private $entity;

    /**
     * A label parser must be associated to a class that has
     * the configurable trait.
     * @param mixed $entity
     */
    function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Returns the plural version of the label
     * @return string
     */
    public function plural()
    {
        return $this->plural;
    }

    /**
     * Returns the singular version of the label
     * @return string
     */
    public function singular()
    {
        return $this->singular;
    }

    /**
     * Analyzes the configuration and the class name to generate
     * both plural and singular human readable forms of this class name.
     * @return null
     */
    public function parse()
    {
        // Fetch the basic values from possible user defined values.
        if ($this->entity->hasConfig("labels")) {
            if ($this->entity->hasConfig("labels.singular_name")) {
                $this->singular = $this->entity->getConfig("labels.singular_name");
            } elseif ($this->entity->hasConfig("labels.name")) {
                $this->plural = $this->entity->getConfig("labels.name");
            }
        }

        if (!empty($this->singular) && empty($this->plural)) {
            $this->plural = Inflector::pluralize($this->singular);
        }

        if (!empty($this->plural) && empty($this->singular)) {
            $this->singular = Inflector::singularize($this->plural);
        }

        // If nothing is sent in, guess the name from the object name.
        if (empty($this->plural) && empty($this->singular)) {
            $this->singular   = ucfirst(Inflector::singularize($this->entity->getShortName()));
            $this->plural     = ucfirst(Inflector::pluralize($this->entity->getShortName()));
        }
    }
}
