<?php
namespace MVC\CustomPostTypes;

use MVC\Utility\Hash;
use MVC\CustomPostTypes\LabeledEntity;

class EntityTable extends LabeledEntity
{
    CONST WP_PREFIX = "cpt_";

    // Table props
   // public static $key         = null;
   // public static $singular    = null;
   // public static $plural      = null;
    public static $options     = array();

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

    public static function createPostType()
    {
        $ClassName = get_called_class();

        // Ensure the default options have been set.
        $customizedOptions = $ClassName::$options + array(
            'labels'              => array(),
            'supports'            => array( 'title' ),
            //'taxonomies'          => null,
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => false,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'rewrite'             => null,
            'capability_type'     => 'post',
        );

        $translations = $ClassName::getTranslationSet();
        $singular   = $translations['singular'];
        $plural     = $translations['plural'];

        $customizedOptions['labels'] += array(
            'name'                => _x( $plural, 'Post Type General Name', $projectKey ),
            'singular_name'       => _x( $singular, 'Post Type Singular Name', $projectKey ),
            'menu_name'           => __( $plural, $projectKey ),
            'parent_item_colon'   => __( $singular. ' Item:', $projectKey ),
            'all_items'           => __( 'All ' . $plural, $projectKey ),
            'view_item'           => __( 'View ' . $singular. ' Item', $projectKey ),
            'add_new_item'        => __( 'Add New', $projectKey ),
            'add_new'             => __( 'Add New', $projectKey ),
            'edit_item'           => __( 'Edit ' . $singular, $projectKey ),
            'update_item'         => __( 'Update ' . $singular, $projectKey ),
            'search_items'        => __( 'Search ' . $plural, $projectKey ),
            'not_found'           => __( 'Not found', $projectKey ),
            'not_found_in_trash'  => __( 'Not found in Trash', $projectKey ),
        );

        register_post_type($ClassName::wordpressKey(), $customizedOptions);

        if (count($customizedOptions['has'])) {
            foreach ($customizedOptions['has'] as $Taxonomy) {
                $Taxonomy::addTaxonomy($ClassName);
            }
        }
    }
}
