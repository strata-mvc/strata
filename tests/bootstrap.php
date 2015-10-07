<?php

use Strata\Strata;

// Require the Fixtures' paths.
$loader = require Strata::getVendorPath() . 'autoload.php';
$loader->setPsr4("Tests\\", 'tests');

// Load up a fake Wordpress wrapper on which the tests will register things.
include('tests/Fixtures/Wordpress/bootstrap.php');

$app = Strata::bootstrap($loader);
$app->setConfig("namespace", "Tests\\Fixtures");
$app->run();
