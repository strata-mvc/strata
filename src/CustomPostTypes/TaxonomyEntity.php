<?php
namespace MVC\CustomPostTypes;

use MVC\Utility\Hash;
use MVC\CustomPostTypes\LabeledEntity;

class TaxonomyEntity extends LabeledEntity
{
    CONST WP_PREFIX = "tax_";

    public  $configuration     = array();

    public static function addTaxonomy($linkedObj)
    {
        $ClassName  = get_called_class();
        $obj = new $ClassName();
        $translations = $ClassName::getLabels();
        $singular   = $translations['singular'];
        $plural     = $translations['plural'];


        $customizedOptions = $obj->configuration + array(
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'update_count_callback'     => '_update_post_term_count',
            'query_var'                 =>  $ClassName::wordpressKey() . "_" . $linkedObj::wordpressKey(),
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'rewrite' => array(
                'with_front' => true,
                'slug' => $ClassName::wordpressKey() . "_" . $linkedObj::wordpressKey()
            ),
            'capabilities' => array(
                'manage_terms' => 'read',
                'edit_terms'   => 'read',
                'delete_terms' => 'read',
                'assign_terms' => 'read',
            ),
            'labels'=> array()
        );

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

        return register_taxonomy($ClassName::wordpressKey(), array($linkedObj::wordpressKey()), $customizedOptions);
    }



}
