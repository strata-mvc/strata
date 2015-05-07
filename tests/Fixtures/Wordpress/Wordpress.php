<?php


class Wordpress {

    private $actions = array();
    private $filters = array();

    public function addAction($type, $callback)
    {
        if (!array_key_exists($type, $this->actions)) {
            $this->actions[$type] = array();
        }

        $this->actions[$type][] = $callback;
    }

    public function isActionPresent()
    {

    }

}
