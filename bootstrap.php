<?php

// Load backend tools that we use, but the frontend may not.
// @todo: This shouldn't really exist. It should be actual HelperObjects.
foreach (array('custom-utils', 'custom-wpml') as $helper) {
    include_once ABSPATH . DIRECTORY_SEPARATOR . 'Vendor' . DIRECTORY_SEPARATOR . 'MVC' .  DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . $helper . '.php';
}

// Prepare the app and load the configuration file
require_once ABSPATH . DIRECTORY_SEPARATOR . 'Vendor' . DIRECTORY_SEPARATOR . 'MVC' .  DIRECTORY_SEPARATOR . 'Mvc.php';

$app = new \MVC\Mvc();
$app->init();

if ($app->ready) {
    // Set up the class autoloader for our classes.
    require_once ABSPATH . DIRECTORY_SEPARATOR . 'Vendor' . DIRECTORY_SEPARATOR . 'MVC' .  DIRECTORY_SEPARATOR . 'Autoloader.php';
    \MVC\Autoloader::register($app);

    // Start the process
    $app->run();
}
