<?php

$wordpress = new Wordpress();

function add_action($type, $callback) {
    $wordpress->add_action($type, $callback);
}
