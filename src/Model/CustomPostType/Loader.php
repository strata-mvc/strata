<?php
namespace MVC\Model\CustomPostType;

use MVC\Utility\Hash;
use MVC\Mvc;

class Loader
{
    public static function preload()
    {
        $namespace = Mvc::getNamespace();
        $customPostTypes = Mvc::config('custom-post-types');

        // Set up the creation of custom post types based on models
        if ($customPostTypes) {
            foreach (Hash::normalize($customPostTypes) as $cpt => $config) {
                // Pipe the creation of the post type object.
                add_action('init', sprintf('%s\\Model\\%s::createPostType', $namespace, $cpt));

                // Do advanced configuration if required.
                if (!is_null($config)) {
                    // Hook in to add admin menu links
                    if (Hash::check($config, 'admin')) {
                        $adminConfig = Hash::extract($config, 'admin');
                        add_action('admin_menu', function () use ($adminConfig, $namespace, $cpt) {
                            $Classname = sprintf('%s\\Model\\%s', $namespace, $cpt);
                            $Classname::addAdminMenus($adminConfig);
                        });
                    }
                }

            }
        }
    }
}
