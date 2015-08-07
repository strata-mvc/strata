<?php
namespace Strata\Model\CustomPostType;

use Strata\Model\Model;
use Strata\Model\CustomPostType\QueriableEntity;

use Strata\Model\CustomPostType\Registrar\CustomPostTypeAdminMenuRegistrar;
use Strata\Model\CustomPostType\Registrar\CustomPostTypeRegistrar;
use Strata\Model\CustomPostType\Registrar\TaxonomyRegistrar;

class Entity extends QueriableEntity
{
    public static function registerPostType()
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

    public static function buildRegisteringCall($cpt)
    {
        $obj = Model::factory($cpt);
        return array($obj, "registerPostType");
    }


    public $wpPrefix = "cpt_";

    /**
     * Returns whether or not the current model supports and has taxonomies.
     * @return boolean True if model has taxonomies
     */
    public function hasTaxonomies()
    {
        return array_key_exists("has", $this->configuration) && count($this->configuration['has'] > 0);
    }

    /**
     * Gets the associated taxonomy objects.
     * @return array
     */
    public function getTaxonomies()
    {
        $tax = array();
        foreach ($this->configuration['has']  as $taxonomyName) {
            $tax[] = Model::factory($taxonomyName);
        }
        return $tax;
    }

    /**
     * Creates a post of the current post type
     * @param (array) options : Options to be sent to wp_insert_post()
     * @return (int) postID
     */
    public function create($options)
    {
        $options += array(
            'post_type'         => self::wordpressKey(),
            'ping_status'       => false,
            'comment_status'    => false
        );

        return wp_insert_post( $options );
    }

    public function update($options)
    {
        return wp_update_post( $options );
    }

    public function delete($postId, $force = false)
    {
        return wp_delete_post( $postId, $force);
    }

    public function registerAdminMenus(array $adminConfig)
    {
        $registration = new CustomPostTypeAdminMenuRegistrar($this);
        $registration->configure($adminConfig);
        $registration->register();
    }
}
