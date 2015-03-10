<?php
// Use composer to load the autoloader.
$loader = require ABSPATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Offer a prettier way of formating debugging output on screen
 */
if (!function_exists('debug')) {
    function debug($var)
    {
        echo "<pre>=======[Debug]=======\n";
        echo "(".gettype($var) . ") " ;
        var_export($var);
        echo "\n\n<div style=\"overflow:auto; font-size: 12px; font-family: consolas; background:transparent; width:100%; height:80px;\">";
        debug_print_backtrace();
        echo "</textarea>";
        echo "\n=======[Debug]=======</pre>";
    }
}

$app = new \MVC\Mvc();
$app->init();

if ($app->ready()) {
    // Add the project's directory to the autoloader
    $loader->setPsr4($app->config['key'] . '\\', get_template_directory() . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "wordpress-mvc" . DIRECTORY_SEPARATOR);

    // Start the process
    $app->run();
}
