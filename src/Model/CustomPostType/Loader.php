<?php
namespace Strata\Model\CustomPostType;

use Strata\Utility\Hash;
use Strata\Strata;
use Strata\Model\Model;
use Strata\Model\CustomPostType\Entity;
use Strata\Model\CustomPostType\Registrar\CustomPostTypeAdminMenuRegistrar;

class Loader
{
    public static function preload()
    {
        $customPostTypes = Strata::config('custom-post-types');

        // Set up the creation of custom post types based on models
        if ($customPostTypes) {
            foreach (Hash::normalize($customPostTypes) as $cpt => $config) {
                add_action('init', Entity::buildRegisteringCall($cpt));

                if (!is_null($config) && Hash::check($config, 'admin')) {

                    $obj = Model::factory($cpt);
                    $registration = new CustomPostTypeAdminMenuRegistrar($obj);
                    $registration->configure(Hash::extract($config, 'admin'));
                    $registration->register();

                }
            }
        }
    }
}
