<?php
namespace MVC\CustomPostTypes;

use MVC\Utility\Hash;
use MVC\Utility\Inflector;
use MVC\Mvc;

class LabeledEntity
{
    public $configuration     = array();

    public static function getShortName($linkToObj = null)
    {
        $rf = new \ReflectionClass( !is_null($linkToObj) ? $linkToObj : get_called_class());
        return $rf->getShortName();
    }

    /**
     * Returns the short key of the current object.
     */
    public static function key()
    {
        $ClassName = get_called_class();
        return strtolower($ClassName::getShortName());
    }

    /**
     * Returns the internal custom post type slug
     */
    public static function wordpressKey()
    {
        $ClassName = get_called_class();
        return $ClassName::WP_PREFIX . $ClassName::key();
    }

    public static function getLabels()
    {
        // Try and generate the labels automatically.
        $singular = "";
        $plural = "";
        $projectKey = strtolower(Mvc::app()->getNamespace());

        $ClassName = get_called_class();
        $obj = new $ClassName();
        $shortname  = strtolower($ClassName::getShortName());

        // Fetch the basic values from possible user defined values.
        if (Hash::check($obj->configuration, "labels")) {
            if (Hash::check($obj->configuration, "labels.singular_name")) {
                $singular = Hash::get($obj->configuration, "labels.singular_name");
            } elseif (Hash::check($obj->configuration, "labels.name")) {
                $plural = Hash::get($obj->configuration, "labels.name");
            }
        }

        if (!empty($singular) && empty($plural)) {
            $plural = Inflector::pluralize($singular);
        }

        if (!empty($plural) && empty($singular)) {
            $singular = Inflector::singularize($plural);
        }

        // If nothing is sent in, guess the name from the object name.
        if (empty($plural) && empty($singular)) {
            $singular   = ucfirst(Inflector::singularize($shortname));
            $plural     = ucfirst(Inflector::pluralize($shortname));
        }

        return array(
            'singular' => $singular,
            'plural' => $plural
        );
    }
}
