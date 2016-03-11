<?php

namespace Strata\Model\CustomPostType\Registrar;

use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\Registrar\Registrar;
use Strata\Utility\Hash;
use Strata\Strata;

/**
 * Registers a custom post type based on a Models' configuration.
 */
class CustomPostTypeRegistrar extends Registrar
{
    /**
     * Attemps to register a post type.
     * @link https://codex.wordpress.org/Function_Reference/register_post_type
     * @return object Returns the result of register_post_type()
     */
    function register()
    {
        // Ensure the default options have been set.
        $customizedOptions = $this->model->getConfiguration() + array(
            'labels'              => array(),
            'supports'            => array('title', 'editor'),
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
            'publicly_queryable'  => true,
            'rewrite'             => array(),
            'capability_type'     => 'post',
        );

        $customizedOptions['rewrite'] += array(
            'with_front' => false,
        );

        $currentLocale = Strata::app()->i18n->getCurrentLocale();
        if (!$currentLocale->isDefault()) {
            $translatedSlug = Hash::get($customizedOptions, "i18n." . $currentLocale->getCode() . ".rewrite.slug");
            if (!is_null($translatedSlug)) {
                $customizedOptions['rewrite']['slug'] = $translatedSlug;
            }
        }

        $labelParser = new LabelParser($this->model);
        $labelParser->parse();
        $singular   = $labelParser->singular();
        $plural     = $labelParser->plural();

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

        return register_post_type($this->model->getWordpressKey(), $customizedOptions);
    }
}
