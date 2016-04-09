<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

$cwd = getcwd();

// Prevent calls form our vendor directory.
// The cwd should always be a project's root.
if ($cwd === dirname(__FILE__)) {
    // Up from 'vendor/francoisfaubert/strata/src/Scripts'
    $projectedRoot = dirname(dirname(dirname(dirname(dirname($cwd)))));
    if (file_exists($projectedRoot) && file_exists($projectedRoot . DIRECTORY_SEPARATOR . 'composer.json')) {
        $cwd = $projectedRoot;
    } else {
        die("[ERROR] Strata could not understand it's working directory.");
    }
}

