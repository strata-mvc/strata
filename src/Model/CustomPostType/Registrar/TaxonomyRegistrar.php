<?php
namespace Strata\Model\CustomPostType\Registrar;

use \Strata\Strata;
use Strata\Model\CustomPostType\Registrar\Registrar;

class TaxonomyRegistrar extends Registrar
{
    public function register()
    {
        $status = true;
        if ($this->entity->hasTaxonomies()) {
            foreach ($this->entity->getTaxonomies() as $taxonomy) {
                $status = $status && $this->registerTaxonomy($taxonomy);
            }
        }
        return $status;
    }

    private function registerTaxonomy($taxonomyClassname)
    {
        $singular   = $this->labelParser->singular();
        $plural     = $this->labelParser->plural();

        $taxonomyKey = $taxonomyClassname::wordpressKey();
        $taxonomy = $taxonomyClassname::staticFactory();
        $key = $this->entity->getWordpressKey() . "_" . $taxonomyKey;

        $customizedOptions = $taxonomy->configuration + array(
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'update_count_callback'     => '_update_post_term_count',
            'query_var'                 =>  $key,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'rewrite' => array(
                'with_front' => true,
                'slug' => $key
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
            'name'                => _x( $plural, 'Post Type General Name', 'strata' ),
            'singular_name'       => _x( $singular, 'Post Type Singular Name', 'strata' ),
            'menu_name'           => __( $plural, 'strata' ),
            'parent_item_colon'   => __( $singular. ' Item:', 'strata' ),
            'all_items'           => __( 'All ' . $plural, 'strata' ),
            'view_item'           => __( 'View ' . $singular. ' Item', 'strata' ),
            'add_new_item'        => __( 'Add New', 'strata' ),
            'add_new'             => __( 'Add New', 'strata' ),
            'edit_item'           => __( 'Edit ' . $singular, 'strata' ),
            'update_item'         => __( 'Update ' . $singular, 'strata' ),
            'search_items'        => __( 'Search ' . $plural, 'strata' ),
            'not_found'           => __( 'Not found', 'strata' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'strata' ),
        );

        return register_taxonomy($taxonomyKey, array($this->entity->getWordpressKey()), $customizedOptions);
    }

    private function hasTaxonomyConfiguration()
    {
        return count($this->getTaxonomyConfiguration()) > 0;
    }

    private function getTaxonomyConfiguration()
    {
        return $this->belongs_to;
    }

}
