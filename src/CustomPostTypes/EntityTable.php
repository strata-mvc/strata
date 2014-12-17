<?php
namespace MVC\CustomPostTypes;

use MVC\Utility\Hash;
use MVC\Mvc;

class EntityTable
{
    // Table props
    public static $key         = null;
    public static $singular    = null;
    public static $plural      = null;
    public static $options     = array();

    /**
     * Creates a post of the current post type
     * @param (array) options : Options to be sent to wp_insert_post()
     * @return (int) postID
     */
    public static function create($options)
    {
        $options += array(
            'post_type'         => self::wpCtpKey(),
            'ping_status'       => false,
            'comment_status'    => false
        );

        return wp_insert_post( $options );
    }

    /**
     * Returns the short key of the current object.
     */
    public static function key()
    {
        $Class = get_called_class();
        return $Class::$key;
    }

    /**
     * Returns the internal custom post type slug
     */
    public static function wpCtpKey()
    {
        return "cpt_" . self::key();
    }

    /**
     * Starts a wrapped wp_query pattern object. Used to chain parameters
     * @return (Query) $query;
     */
    public static function query()
    {
        $query = new Query();
        return $query->type(self::wpCtpKey());
    }

    public static function taxonomy($taxonomy)
    {
        $query = new TermsQuery();
        return $query->taxonomy($taxonomy);
    }

    public static function getTaxonomyKey($linkToObj)
    {
        $rf = new \ReflectionClass($linkToObj);
        return sprintf("%s_%s", self::key(), strtolower($rf->getShortName()));
    }

    public static function findAll()
    {
        return self::query()->fetch();
    }

    public static function createPostType()
    {
        $ClassName = get_called_class();
        $obj = new $ClassName();

        $labels = array(
            'name'                => _x( $obj::$plural, 'Post Type General Name', PROJECT_KEY ),
            'singular_name'       => _x( $obj::$singular, 'Post Type Singular Name', PROJECT_KEY ),
            'menu_name'           => __( $obj::$plural, PROJECT_KEY ),
            'parent_item_colon'   => __( '$obj ' . $obj::$singular . ' Item:', PROJECT_KEY ),
            'all_items'           => __( 'All ' . $obj::$plural, PROJECT_KEY ),
            'view_item'           => __( 'View ' . $obj::$singular . ' Item', PROJECT_KEY ),
            'add_new_item'        => __( 'Add New', PROJECT_KEY ),
            'add_new'             => __( 'Add New', PROJECT_KEY ),
            'edit_item'           => __( 'Edit ' . $obj::$singular, PROJECT_KEY ),
            'update_item'         => __( 'Update ' . $obj::$singular, PROJECT_KEY ),
            'search_items'        => __( 'Search ' . $obj::$plural, PROJECT_KEY ),
            'not_found'           => __( 'Not found', PROJECT_KEY ),
            'not_found_in_trash'  => __( 'Not found in Trash', PROJECT_KEY ),
        );

        // Set the remainder from the default values
        $customizedOptions = $obj::$options;
        $customizedOptions += array(
            'label'               => __( $obj::$plural, PROJECT_KEY ),
            'description'         => __( $obj::$plural, PROJECT_KEY ),
            'labels'              => $labels,
            'labels'              => null,
            'supports'            => array( 'title' ),
            'taxonomies'          => array(),
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
            'rewrite'             => array(),
            'capability_type'     => 'post',
        );

        register_post_type($obj::wpCtpKey(), $customizedOptions);

        return $obj;
    }


    public static function linkTo($linkToObj, $options = array())
    {
        $ClassName = get_called_class();
        $obj = new $ClassName();

        $labels = array(
            'name'                       => _x( $linkToObj::$plural, 'Taxonomy General Name', PROJECT_KEY ),
            'singular_name'              => _x( 'User type', 'Taxonomy Singular Name', PROJECT_KEY ),
            'menu_name'                  => __( $linkToObj::$plural, PROJECT_KEY ),
            'all_items'                  => __( 'All ' . $linkToObj::$plural, PROJECT_KEY ),
            'parent_item'                => __( 'Parent ' . $linkToObj::$singular, PROJECT_KEY ),
            'parent_item_colon'          => __( 'Parent ' . $linkToObj::$singular .':', PROJECT_KEY ),
            'new_item_name'              => __( 'New ' . $linkToObj::$singular .' Name', PROJECT_KEY ),
            'add_new_item'               => __( 'Add New ' . $linkToObj::$singular, PROJECT_KEY ),
            'edit_item'                  => __( 'Edit ' . $linkToObj::$singular, PROJECT_KEY ),
            'update_item'                => __( 'Update ' . $linkToObj::$singular, PROJECT_KEY ),
            'separate_items_with_commas' => __( 'Separate '.$linkToObj::$plural.' with commas', PROJECT_KEY ),
            'search_items'               => __( 'Search '. $linkToObj::$plural, PROJECT_KEY ),
            'add_or_remove_items'        => __( 'Add or remove ' . $linkToObj::$plural, PROJECT_KEY ),
            'choose_from_most_used'      => __( 'Choose from the most used ' . $linkToObj::$plural, PROJECT_KEY ),
            'not_found'                  => __( 'Not Found', PROJECT_KEY ),
        );


        $rf = new \ReflectionClass($linkToObj);
        $taxslug = $obj::key() . "_" . strtolower($rf->getShortName());
        $taxkey = "tax_" . $taxslug;

        $options += array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'update_count_callback'     => '_update_post_term_count',
            'query_var'                 => $obj::key(),
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'rewrite' => array(
                'with_front' => true,
                'slug' => $taxslug
            ),
            'capabilities' => array(
                'manage_terms' => 'read',
                'edit_terms'   => 'read',
                'delete_terms' => 'read',
                'assign_terms' => 'read',
            ),
        );


        register_taxonomy($taxkey, $obj::key(), $options);

        return $obj;
    }
}
