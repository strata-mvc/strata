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

// Load the class loader
require_once $cwd . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $cwd . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'application.php';

use Strata\Strata;

$app = Strata::bootstrap(Strata::requireVendorAutoload());

$app->init();
$app->takeOverWPCLIArgs();

// Get and run the shell.
\Strata\Shell\Shell::run();
