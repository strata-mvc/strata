<?php
namespace MVC\CustomPostTypes;

use MVC\Utility\Hash;
use MVC\Utility\Inflector;
use MVC\Mvc;

class LabeledEntity
{
    public static $options     = array();

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

    public static function getTranslationSet()
    {
        // Try and generate the labels automatically.
        $singular = "";
        $plural = "";
        $projectKey = strtolower(Mvc::app()->getNamespace());

        $ClassName = get_called_class();
        $shortname  = strtolower($ClassName::getShortName());

        // Fetch the basic values from possible user defined values.
        if (Hash::check($ClassName::$options, "labels")) {
            if (Hash::check($ClassName::$options, "labels.singular_name")) {
                $singular = Hash::get($ClassName::$options, "labels.singular_name");
            } elseif (Hash::check($ClassName::$options, "labels.name")) {
                $plural = Hash::get($ClassName::$options, "labels.name");
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
