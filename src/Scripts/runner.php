#!/usr/bin/php -q
<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

// Load the class loader
require $argv[1] . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$shell = null;
if (count($argv) > 1) {
    $shell = \Strata\Shell\Shell::factory($argv[2]);
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
