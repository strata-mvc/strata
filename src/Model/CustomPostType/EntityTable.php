<?php
namespace MVC\Model\CustomPostType;

use MVC\Utility\Hash;
use MVC\Utility\Inflector;
use MVC\Mvc;
use MVC\Router;
use MVC\Model\CustomPostType\LabeledEntity;
use MVC\Model\CustomPostType\Query;

class EntityTable extends LabeledEntity
{
    CONST WP_PREFIX = "cpt_";

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

    public static function createPostType()
    {
        $ClassName = get_called_class();
        $obj = new $ClassName();

        // Ensure the default options have been set.
        $customizedOptions = $obj->configuration + array(
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

        $translations = $ClassName::getLabels();
        $singular   = $translations['singular'];
        $plural     = $translations['plural'];
        $projectKey = strtolower(Mvc::app()->getNamespace());

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

        if (array_key_exists('has', $customizedOptions) && count($customizedOptions['has'])) {
            foreach ($customizedOptions['has'] as $Taxonomy) {
                $Taxonomy::addTaxonomy($ClassName);
            }
        }
    }

    /**
    *  Add backend menu links for the model
    */
    public static function addAdminMenus($cptAdminConfig)
    {
        $ClassName = get_called_class();
        $obj = new $ClassName();
        $parentSlug = 'edit.php?post_type=' . $ClassName::wordpressKey();
        $namespace = \MVC\Mvc::getNamespace();

        foreach ($cptAdminConfig as $func => $config) {
            $config += array(
                'title'         => ucfirst($func),
                'menu-title'    => ucfirst($func),
                'capability'    => "manage_options",
                'icon'          => null,
                'route'         => array(Inflector::pluralize($ClassName::getShortName()) . "Controller", $func),
                'position'      => null,
            );

            // This is to circumvent that wordpress doesn't let you pass arguments to
            // callbacks so we can send the controller and function to the router.
            // We dont want people to have to specify that odd function name.
            // Allow them to send the controller string name and take care of the rest.
            if (is_string($config['route'])) {
                $route = Router::callback($config['route'], $func);
            }  else {
                $route = Router::callback($config['route'][0], $config['route'][1]);
            }

            add_submenu_page($parentSlug, $config['title'], $config['menu-title'], $config['capability'], $func, $route, $config['icon'], $config['position']);
        }
    }
}
