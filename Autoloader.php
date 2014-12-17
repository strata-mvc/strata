<?php
namespace MVC;

use Exception;

class Autoloader {

    public static $app = null;

    public static function load($class)
    {
        $locations = array(
            get_template_directory() . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "MVC" . DIRECTORY_SEPARATOR,
            ABSPATH . "Vendor" . DIRECTORY_SEPARATOR
        );

        // Remove double \
        $contextualClassPath = preg_replace("/\\\\/", DIRECTORY_SEPARATOR, $class);
        // Remove project name from the namespacing as it's conventionnaly implied.
        $projectKey = Autoloader::$app->getNamespace();
        $contextualClassPath = preg_replace("/^$projectKey/", "", $contextualClassPath);

        while($location = array_shift($locations)) {
            if (file_exists($location . $contextualClassPath . ".php")) {
                require_once($location . $contextualClassPath . ".php");
                return;
            }
        }

        // We can't throw because wordpress plugins don't encapsulate their requirements very well.
        //throw new Exception("Could not autoload class $class as $contextualClassPath.");
    }

    public static function register($app)
    {
        Autoloader::$app = $app;
        spl_autoload_register( array('MVC\Autoloader', 'load') );
    }
}
