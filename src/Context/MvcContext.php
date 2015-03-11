<?php
namespace MVC\Context;

use MVC\Mvc;
use MVC\Utility\Hash;

class MvcContext {

    /**
     * @var composer Composer's loader object. Kept handy in case additional paths
     * need to be added along the way.
     */
    public static $loader = null;

    /**
     * Because the processing is called asynchronously using wordpress' hooks,
     * we will lose the referece to the kickstarter object.
     * we need to make sure the config values are availlable through global values.
     */
    public static function app()
    {
        return $GLOBALS['__MVC__'];
    }

    /**
     * Fetches a value in the app's configuration array
     * @param string $key In dot-notation format
     * @return mixed
     */
    public static function config($key)
    {
        $app = Mvc::app();
        return $app->read($key);
    }

    /**
     * Return the current project's namespace key
     */
    public static function getNamespace()
    {
        return self::config('key');
    }

    public static function loadEnvConfiguration($env = "production")
    {
        $filepath = self::getConfigurationPath() . $env . ".ini";

        foreach (parse_ini_file($filepath) as $key => $value) {
            if (!defined($key)) {
                define(strtoupper($key), $value);
            }
        }
    }

    public static function bootstrap()
    {
        $app = new \MVC\Mvc();

        // Expose the app context to the current process.
        $GLOBALS['__MVC__'] = $app;

        $app->init();

        if ($app->ready()) {
            // Add the project's directory to the autoloader
            self::addPsr4(self::getNamespace(), self::getSRCPath());

            // Start the process
            $app->run();
        }
    }

    /**
     * Appends a PSR4 rule to composer's loader.
     * @param [type] $key  root path
     * @param [type] $path location of the files
     */
    public static function addPsr4($key, $path)
    {
        if (!substr($key, -2) != "\\") {
            $key = $key . "\\";
        }

        return \MVC\Mvc::$loader->setPsr4($key, $path);
    }

    public static function getSRCPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(MVC_ROOT_PATH, "src")) . DIRECTORY_SEPARATOR;
    }

    public static function getUtilityPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getOurVendorPath(), "src", "Utility")) . DIRECTORY_SEPARATOR;
    }

    public static function getConfigurationPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(MVC_ROOT_PATH, "config")) . DIRECTORY_SEPARATOR;
    }

    public static function getVendorPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(MVC_ROOT_PATH, "vendor")) . DIRECTORY_SEPARATOR;
    }

    public static function getOurVendorPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getVendorPath(), "francoisfaubert", "wordpress-mvc")) . DIRECTORY_SEPARATOR;
    }

    public static function getProjectConfigurationFilePath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getConfigurationPath(), 'app.php'));
    }
}
