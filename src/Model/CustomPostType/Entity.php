<?php
namespace Strata\Model\CustomPostType;

use Strata\Utility\Hash;
use Strata\Utility\Inflector;
use Strata\Strata;
use Strata\Router;

use Strata\Model\WordpressEntity;
use Strata\Model\CustomPostType\Query;
use Strata\Model\CustomPostType\Registrar\CustomPostTypeRegistrar;
use Strata\Model\CustomPostType\Registrar\TaxonomyRegistrar;

use Strata\Model\Model;

class Entity extends WordpressEntity
{

    public $wpPrefix = "cpt_";

    /**
     * Creates a post of the current post type
     * @param (array) options : Options to be sent to wp_insert_post()
     * @return (int) postID
     */
    public static function create($options)
    {
        $options += array(
            'post_type'         => self::wordpressKey(),
            'ping_status'       => false,
            'comment_status'    => false
        );

        return wp_insert_post( $options );
    }

    public static function update($options)
    {
        return wp_update_post( $options );
    }

    public static function wp_delete_post($postId, $force = false)
    {
        return wp_delete_post( $postId, $force);
    }

    /**
     * Starts a wrapped wp_query pattern object. Used to chain parameters
     * @return (Query) $query;
     */
    public static function query()
    {
        $query = new Query();
        return $query->type(self::wordpressKey());
    }

    public static function findAll()
    {
        return self::query()->fetch();
    }

    public static function count()
    {
        return wp_count_posts(self::wordpressKey());
    }

    public static function buildRegisteringCall($cpt)
    {
        $obj = Model::factory($cpt);
        return array($obj, "registerPostType");
    }

    public function registerPostType()
    {
        $obj = self::staticFactory();

        $registrars = array(
            new CustomPostTypeRegistrar($obj),
            new TaxonomyRegistrar($obj)
        );

        foreach ($registrars as $registrar) {
            $registrar->register();
        }
    }
}
