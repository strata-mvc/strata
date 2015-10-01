<?php
namespace Strata\Model\CustomPostType;

use Strata\Utility\Inflector;

class LabelParser
{
    private $plural = "";
    private $singular = "";
    private $entity;

    function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function plural()
    {
        return $this->plural;
    }

    public function singular()
    {
        return $this->singular;
    }

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
