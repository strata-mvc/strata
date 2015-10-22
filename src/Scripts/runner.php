<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

// Load the class loader
require_once getcwd() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Strata\Strata;

$app = Strata::bootstrap(Strata::requireVendorAutoload());
$app->init();
$app->takeOverWPCLIArgs();

// Get and run the shell.
\Strata\Shell\Shell::run();
