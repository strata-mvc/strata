#!/usr/bin/php -q
<?php

// Load the class loader
define("MVC_ROOT_PATH", realpath(__DIR__ . '/../'));
require MVC_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$shell = null;
if (count($argv) > 0) {
    $shell = \MVC\Shell\Shell::factory($argv[1]);
}

if (is_null($shell)) {
     echo "This is not a a valid command.";
     echo "";
} else {
    $shell->initialize();
    $shell->contextualize($argv);
    $shell->main();
}
