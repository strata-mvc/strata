#!/usr/bin/php -q
<?php
/**
 * Command-line code generation utility to automate programmer chores.
 * @see https://github.com/cakephp/app/blob/master/bin/cake.php
 */

define("MVC_ROOT_PATH", realpath(__DIR__ . '/../'));

// Use composer to load the autoloader.
$loader = require MVC_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$shell = \MVC\Shell\Shell::factory($argv[1]);

if (is_null($shell)) {
     echo "This is not a a valid command.";
     echo "";

} else {
    $shell->initialize();
    $shell->contextualize($argv);
    $shell->main();
}
