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
        echo "[ERROR] Strata could not understand it's working directory.";
        return;
    }
}

// Because of the way we need to be in a running Strata instance before running
// the shell script, there's a global reference already available (and configured).
// The only case where it does not exist is when runnign db create.
if (!class_exists('Strata\\Strata')) {
    require_once $cwd . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    require_once $cwd . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'application.php';
    $app = \Strata\Strata::bootstrap(\Strata\Strata::requireVendorAutoload());
    $app->init();
}

// Make strata understand that the server variables are being
// set by WP-CLI.
\Strata\Strata::app()->takeOverWPCLIArgs();

// Get and run the shell.
\Strata\Shell\Shell::run();
