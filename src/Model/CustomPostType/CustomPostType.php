<?php

namespace Strata\Model\CustomPostType;

use Strata\Utility\Hash;
use Strata\Model\WordpressEntity;
use Strata\Model\Taxonomy\Taxonomy;
use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\Registrar\CustomPostTypeAdminMenuRegistrar;
use Strata\Model\CustomPostType\Registrar\CustomPostTypeRegistrar;
use Strata\Model\CustomPostType\Registrar\TaxonomyRegistrar;
use Strata\Model\CustomPostType\QueriableEntityTrait;

/**
 * A class that wraps around Wordpress' custom post types.
 */
class CustomPostType extends WordpressEntity
{
    use QueriableEntityTrait;

    /**
     * The Wordpress custom post type identifier prefix
     * @var string
     */
    public $wpPrefix = "cpt_";

    /**
     * A list of administration sub-menus associated to the
     * custom post type.
     * @var array
     */
    public $admin_menus = array();

    /**
     * A list of taxonomies associated to the custom post type.
     * @var array
     */
    public $belongs_to = array();

    /**
     * Specifies whether Strata should attempt to automate routing
     * to the model's default controller when the custom post type's
     * slug is matched in the URL.
     * @var boolean
     */
    public $routed = false;

    /**
     * Returns a label object that exposes singular and plural labels
     * @return LabelParser
     */
    public function getLabel()
    {
        $labelParser = new LabelParser($this);
        $labelParser->parse();
        return $labelParser;
    }

    /**
     * Registers the custom post type in Wordpress. A Custom post type
     * must trigger this during the 'init' state for it to be recognized
     * automatically by Wordpress.
     */
    public function register()
    {
        $registrars = array(
            new CustomPostTypeRegistrar($this),
            new TaxonomyRegistrar($this)
        );

        foreach ($registrars as $registrar) {
            $registrar->register();
        }
    }

    /**
     * Registers the custom post type's sub menus.
     * @return boolean The result of the registration
     */
    public function registerAdminMenus()
    {
        $registration = new CustomPostTypeAdminMenuRegistrar($this);
        $registration->configure(Hash::normalize($this->admin_menus));
        return $registration->register();
    }

    /**
     * Returns the model's menu icon as specified by the 'menu_icon'
     * configuration key.
     * @return string
     */
    public function getIcon()
    {
        if (array_key_exists('menu_icon', $this->configuration)) {
            return $this->configuration['menu_icon'];
        }

        return 'dashicons-admin-post';
    }

    /**
     * Returns whether or not the current model supports and has taxonomies.
     * @return boolean True if model has taxonomies
     */
    public function hasTaxonomies()
    {
        return count($this->belongs_to) > 0;
    }

    /**
     * Gets the associated taxonomy objects.
     * @return array
     */
    public function getTaxonomies()
    {
        $tax = array();

        foreach (Hash::normalize($this->belongs_to) as $taxonomyName => $taxonomyConfig) {
            if (class_exists($taxonomyName)) {
                $tax[] = new $taxonomyName();
            } else {
                $tax[] = Taxonomy::factory($taxonomyName);
            }
        }

        return $tax;
    }

    /**
     * Creates a post of the current post type based on the
     * passed options.
     * @param array $options Options to be sent to wp_insert_post()
     * @return int The created post id
     */
    public function create($options)
    {
        $options += array(
            'post_type'         => $this->getWordpressKey(),
            'ping_status'       => false,
            'comment_status'    => false
        );

        return wp_insert_post($options);
    }

    /**
     * Updates a post of the current post type based on the
     * passed options.
     * @param array $options Options to be sent to wp_update_post()
     * @return boolean Whether something was updated
     */
    public function update($options)
    {
        return wp_update_post($options);
    }

    /**
     * Deletes a post of the current post type by its ID
     * @param  int  $postId
     * @param  boolean $force  (optional)
     * @return boolean Whether something was updated
     */
    public function delete($postId, $force = false)
    {
        return wp_delete_post($postId, $force);
    }
}
