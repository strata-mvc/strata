<?php

namespace Strata\Model\CustomPostType\Registrar;

use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\Registrar\Registrar;
use Strata\Model\Taxonomy\Taxonomy;

use Strata\Strata;
use Strata\Utility\Hash;

/**
 * Registers a taxonomy based on a Models' configuration.
 */
class TaxonomyRegistrar extends Registrar
{
    /**
     * Attemps to register a taxonomy on a post type.
     * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    public function register()
    {
        if ($this->model->hasTaxonomies()) {
            foreach ($this->model->getTaxonomies() as $taxonomy) {
                $this->registerTaxonomy($taxonomy);

                if ($this->shouldAddRoutes($taxonomy)) {
                    $this->addResourceRoute($taxonomy);
                }
            }
        }
    }
    /**
     * Specifies whether the $customPostType should be routed as
     * a resource.
     * @param  Taxonomy $taxonomy
     * @return boolean
     */
    private function shouldAddRoutes(Taxonomy $taxonomy)
    {
        return !is_admin() && ((bool)$taxonomy->routed === true || is_array($taxonomy->routed));
    }

    /**
     * Adds a new resource route to the router attached to the current
     * app.
     * @param Taxonomy $taxonomy
     */
    private function addResourceRoute(Taxonomy $taxonomy)
    {
        Strata::router()->addResource($taxonomy);
    }

    /**
     * Registers a taxonomy
     * @param  Taxonomy $taxonomy The taxonomy model
     * @return object What is being returned by register_taxonomy
     */
    private function registerTaxonomy(Taxonomy $taxonomy)
    {
        $labelParser = new LabelParser($taxonomy);
        $labelParser->parse();
        $singular   = $labelParser->singular();
        $plural     = $labelParser->plural();

        $key = $this->model->getWordpressKey() . "_" . $taxonomy->getWordpressKey();

        $customizedOptions = $taxonomy->getConfiguration() + array(
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'update_count_callback'     => '_update_post_term_count',
            'query_var'                 =>  $key,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'rewrite' => array(),
            'capabilities' => array(),
            'labels'=> array()
        );

        $customizedOptions['capabilities'] += array(
            'manage_terms' => 'read',
            'edit_terms'   => 'read',
            'delete_terms' => 'read',
            'assign_terms' => 'read',
        );


        $customizedOptions['rewrite'] += array(
            'with_front' => true,
            'slug' => $key
        );

        $i18n = Strata::i18n();
        if ($i18n->isLocalized()) {
            $currentLocale = $i18n->getCurrentLocale();
            if ($currentLocale && !$currentLocale->isDefault()) {
                $translatedSlug = Hash::get($customizedOptions, "i18n." . $currentLocale->getCode() . ".rewrite.slug");
                if (!is_null($translatedSlug)) {
                    $customizedOptions['rewrite']['slug'] = $translatedSlug;
                }
            }
        }

        $customizedOptions['labels'] += array(
            'name'                => _x($plural, 'Post Type General Name', 'strata'),
            'singular_name'       => _x($singular, 'Post Type Singular Name', 'strata'),
            'menu_name'           => __($plural, 'strata'),
            'parent_item_colon'   => __($singular. ' Item:', 'strata'),
            'all_items'           => __('All ' . $plural, 'strata'),
            'view_item'           => __('View ' . $singular. ' Item', 'strata'),
            'add_new_item'        => __('Add New', 'strata'),
            'add_new'             => __('Add New', 'strata'),
            'edit_item'           => __('Edit ' . $singular, 'strata'),
            'update_item'         => __('Update ' . $singular, 'strata'),
            'search_items'        => __('Search ' . $plural, 'strata'),
            'not_found'           => __('Not found', 'strata'),
            'not_found_in_trash'  => __('Not found in Trash', 'strata'),
        );

        $wordpressKey = $taxonomy->getWordpressKey();
        Strata::app()->setConfig("runtime.taxonomy.query_vars.$wordpressKey", $customizedOptions['query_var']);

        return register_taxonomy($wordpressKey, array($this->model->getWordpressKey()), $customizedOptions);
    }
}
