<?php
namespace MVC\CustomPostTypes;

use MVC\Utility\Hash;
use MVC\CustomPostTypes\LabeledEntity;

class TaxonomyEntity extends LabeledEntity
{
    CONST WP_PREFIX = "tax_";

    public static $options     = array();

    public static function addTaxonomy($linkedObj)
    {
        $ClassName  = get_called_class();
        $translations = $ClassName::getTranslationSet();
        $singular   = $translations['singular'];
        $plural     = $translations['plural'];


        $customizedOptions = $ClassName::$options + array(
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
        );

        $defaultLabels = array(
            'name'                       => _x( $plural, 'Taxonomy General Name', $projectKey ),
            'singular_name'              => _x( $singular . ' type', 'Taxonomy Singular Name', $projectKey ),
            'menu_name'                  => __( $plural, $projectKey ),
            'all_items'                  => __( 'All ' . $plural, $projectKey ),
            'parent_item'                => __( 'Parent ' . $singular, $projectKey ),
            'parent_item_colon'          => __( 'Parent ' . $singular .':', $projectKey ),
            'new_item_name'              => __( 'New ' . $singular .' Name', $projectKey ),
            'add_new_item'               => __( 'Add New ' . $singular, $projectKey ),
            'edit_item'                  => __( 'Edit ' . $singular, $projectKey ),
            'update_item'                => __( 'Update ' . $singular, $projectKey ),
            'separate_items_with_commas' => __( 'Separate '.$plural.' with commas', $projectKey ),
            'search_items'               => __( 'Search '. $plural, $projectKey ),
            'add_or_remove_items'        => __( 'Add or remove ' . $plural, $projectKey ),
            'choose_from_most_used'      => __( 'Choose from the most used ' . $plural, $projectKey ),
            'not_found'                  => __( 'Not Found', $projectKey ),
        );

        if (Hash::check($ClassName::$options, "labels")) {
            $customizedOptions['labels'] = $ClassName::$options["labels"] + $defaultLabels;
        }

        return register_taxonomy($ClassName::wordpressKey(), array($linkedObj::wordpressKey()), $customizedOptions);
    }



}
