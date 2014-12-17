<?php
namespace MVC\CustomPostTypes;

class Loader
{
    public static function preload($app)
    {
        // Set up the creation of custom post types based on models
        if (array_key_exists('custom-post-types', $app->config)) {
            $pattern = '%s\\Models\\%s::createPostType';
            foreach ($app->config['custom-post-types'] as $ctp) {
                add_action('init', sprintf($pattern, $app->getNamespace(), $ctp));
            }
        }
    }
}
