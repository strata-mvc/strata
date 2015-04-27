<?php
namespace Strata\Context;

use Strata\Strata;
use Strata\Utility\Hash;

class StrataContext {

    const DEFAULT_NAMESPACE = "App";

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
        return $GLOBALS['__Strata__'];
    }

    /**
     * Fetches a value in the app's configuration array
     * @param string $key In dot-notation format
     * @return mixed
     */
    public static function config($key)
    {
        $app = Strata::app();
        return $app->read($key);
    }

    /**
     * Return the current project's namespace key
     */
    public static function getNamespace()
    {
        $var = self::config('namespace');
        $namespace = array_pop($var);

        if (!is_null($namespace)) {
            return $namespace;
        }

        return self::DEFAULT_NAMESPACE;
    }

    public static function loadEnvConfiguration($env = "production")
    {
        $filepath = self::getConfigurationPath() . $env . ".ini";
        if (file_exists($filepath)) {
            foreach (parse_ini_file($filepath) as $key => $value) {
                if (!defined($key)) {
                    define(strtoupper($key), $value);
                }
            }
        }
    }

    public static function bootstrap()
    {
        $app = new \Strata\Strata();

        // Expose the app context to the current process.
        $GLOBALS['__Strata__'] = $app;

        $app->init();

        if ($app->ready()) {
            // Add the project's directory to the autoloader
            //self::addPsr4(self::getNamespace(), self::getSRCPath());

            // Start the process
            $app->run();
        }
    }

    /* *
     * Appends a PSR4 rule to composer's loader.
     * @param [type] $key  root path
     * @param [type] $path location of the files

    public static function addPsr4($key, $path)
    {
        if (!substr($key, -2) != "\\") {
            $key = $key . "\\";
        }

        return \Strata\Strata::$loader->setPsr4($key, $path);
    }*/

    public static function getRootPath()
    {
        if (defined('ABSPATH')) {
            return dirname(dirname(ABSPATH));
        }
        return getcwd();
    }

    public static function getThemesPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "web", "app", "themes")) . DIRECTORY_SEPARATOR;
    }

    public static function getSRCPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "src")) . DIRECTORY_SEPARATOR;
    }

    public static function getUtilityPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getOurVendorPath(), "src", "Utility")) . DIRECTORY_SEPARATOR;
    }

    public static function getConfigurationPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "config")) . DIRECTORY_SEPARATOR;
    }

    public static function getVendorPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "vendor")) . DIRECTORY_SEPARATOR;
    }

    public static function getOurVendorPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getVendorPath(), "francoisfaubert", "wordpress-mvc")) . DIRECTORY_SEPARATOR;
    }

    public static function getProjectConfigurationFilePath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getConfigurationPath(), 'strata.php'));
    }
}
