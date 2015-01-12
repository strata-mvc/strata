<?php
namespace MVC\CustomPostTypes;
use MVC\Utility\Hash;

class Loader
{
    public static function preload($app)
    {
        $namespace = $app->getNamespace();

        // Set up the creation of custom post types based on models
        if (array_key_exists('custom-post-types', $app->config) && is_array($app->config['custom-post-types'])) {
            $pattern = '%s\\Models\\%s::createPostType';
            foreach ($app->config['custom-post-types'] as $ctp) {
                add_action('init', sprintf($pattern, $namespace, $ctp));
            }
        }
    }
}
