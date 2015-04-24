#!/usr/bin/php -q
<?php

// Load the class loader
define("MVC_ROOT_PATH", realpath(__DIR__ . '/../'));
require $argv[1] . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$shell = null;
if (count($argv) > 1) {
    $shell = \MVC\Shell\Shell::factory($argv[2]);
}

if (is_null($shell)) {
     echo "This is not a a valid command.";
     echo "";
} else {
    try {
        $shell->initialize();
        $shell->contextualize($argv);
        $shell->main();
    } catch (Exception $e) {
        echo $e->getMessage() . "\n\n";
    }
}
