#!/usr/bin/php -q
<?php
/**
 * Command-line code generation utility to automate programmer chores.
 * @see https://github.com/cakephp/app/blob/master/bin/cake.php
 */

define("MVC_ROOT_PATH", realpath(__DIR__ . '/../'));

// Use composer to load the autoloader.
$loader = require MVC_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
\MVC\Mvc::loadEnvConfiguration();


if (!defined("MVC_APP_NAMESPACE")) {
    throw new Exception("'MVC_APP_NAMESPACE' must be set.");
}

$shell = null;
switch($argv[1]) {
    case "c" : case "console" :
        $shell =  new \MVC\Shell\ConsoleShell();
        break;
    case "s" : case "server" :
        $shell =  new \MVC\Shell\ServerShell();
        break;
    case "g" : case "generate" :
        $shell =  new \MVC\Shell\GenerateShell();
        break;
}

if (is_null($shell)) {
     echo "This is not a a valid command.";
} else {
    $shell->initialize();
    $shell->contextualize($argv);
    $shell->main();
}
