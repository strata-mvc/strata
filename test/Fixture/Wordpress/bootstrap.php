<?php

$GLOBALS['__Wordpress__'] = new \Test\Fixture\Wordpress\Wordpress();

function wordpress()
{
    return $GLOBALS['__Wordpress__'];
}

function add_action($type, $callback)
{
    wordpress()->add_action($type, $callback);
}

function add_shortcode($tag, $func)
{
    wordpress()->add_shortcode($tag, $func);
}

function get_template_directory()
{
    return 'test/Fixture';
}

function get_post_status()
{
    return "published";
}

function is_admin()
{
    return true;
}
