<?php
namespace Tests\Fixtures\Wordpress;

use Exception;

class Wordpress {

    public $actions = array();
    public $filters = array();
    public $shortcodes = array();

    public function reset()
    {
        $this->actions = array();
        $this->filters = array();
        $this->shortcodes = array();
    }

    public function add_action($type, $callback)
    {
        if (!array_key_exists($type, $this->actions)) {
            $this->actions[$type] = array();
        }

        $this->actions[$type][] = $callback;
    }

    function add_shortcode($tag, $func)
    {
        if (array_key_exists($tag, $this->shortcodes)) {
            throw new Exception("Shortcode already defined");
        }

        $this->shortcodes[$tag] = $func;
    }

}
