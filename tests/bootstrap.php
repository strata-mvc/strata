<?php

// We need a starting point to start testing the framework.
// I doubt it's super clean to include Strata without testing
// it, but I am not sure how to kickoff testing otherwise.


// Require the Fixtures' paths.
$loader = require \Strata\Strata::getVendorPath() . 'autoload.php';
$loader->setPsr4("Tests\\", \Strata\Strata::getTestPath());

// Load up a fake Wordpress wrapper on which the tests will register things.
include('Fixtures/Wordpress/bootstrap.php');

// Expose the app context to the current process.
$app = new \Tests\Fixtures\Strata\Strata();
$app->setLoader($loader);


$GLOBALS['__Strata__'] = $app;
