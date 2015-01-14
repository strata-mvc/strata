<?php
namespace MVC\CustomPostTypes;
use MVC\Utility\Hash;

class Loader
{
    public static function preload()
    {
        $app = \MVC\Mvc::app();
        $namespace = $app->getNamespace();

        // Set up the creation of custom post types based on models
        if (array_key_exists('custom-post-types', $app->config) && is_array($app->config['custom-post-types'])) {
            foreach (Hash::normalize($app->config['custom-post-types']) as $cpt => $config) {
                // Pipe the creation of the post type object.
                add_action('init', sprintf('%s\\Models\\%s::createPostType', $namespace, $cpt));

                // Do advanced configuration if required.
                if (!is_null($config)) {
                    // Hook in to add admin menu links
                    if (Hash::check($config, 'admin')) {
                        $adminConfig = Hash::extract($config, 'admin');
                        add_action('admin_menu', function () use ($adminConfig, $namespace, $cpt) {
                            $Classname = sprintf('%s\\Models\\%s', $namespace, $cpt);
                            $Classname::addAdminMenus($adminConfig);
                        });
                    }
                }

            }
        }
    }
}
