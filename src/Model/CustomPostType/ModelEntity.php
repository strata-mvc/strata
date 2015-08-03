<?php
namespace Strata\Model\CustomPostType;

use Exception;

class ModelEntity
{

        /**
     * Generates a possible namespace and classname combination of a
     * Strata controller. Mainly used to avoid hardcoding the '\\Controller\\'
     * string everywhere.
     * @param  string $name The class name of the controller
     * @return string       A fulle namespaced controller name
     */
    public static function generateClassPath($name)
    {
        return Strata::getNamespace() . "\\Model\\Entity\\" . self::generateClassName($name);
    }

    public static function generateClassName($name)
    {
        $name = str_replace("-", "_", $name);
        return Inflector::classify($name);
    }

    private $wpPost;

    function __construct(\WP_Post $post)
    {
        $this->wpPost = $post;
    }

    function __get($var)
    {
        if (is_null($this->wpPost)) {
            throw new Exception('ModelEntity was not linked to a Wordpress post.');
        }

        return $this->wpPost->{$var};
    }

}
