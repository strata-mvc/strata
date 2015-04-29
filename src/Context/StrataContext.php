<?php
namespace Strata\Context;

use Strata\Strata;
use Strata\Utility\Hash;

use Composer\Autoload\ClassLoader;

/**
 * Contains all the static methods related to the Strata app object.
 */
class StrataContext {

    /**
     * Because the processing is called asynchronously using wordpress' hooks,
     * we will lose the referece to the kickstarter object.
     * we need to make sure the config values are availlable through global values.
     */
    public static function app()
    {
        if (array_key_exists('__Strata__', $GLOBALS)) {
            return $GLOBALS['__Strata__'];
        }
    }

    /**
     * Fetches a value in the app's configuration array
     * @param string $key In dot-notation format
     * @return mixed
     */
    public static function config($key)
    {
        $app = Strata::app();
        if (!is_null($app)) {
            return $app->read($key);
        }
    }

    /**
     * Returns the current project's namespace key
     * @return  string A namespace
     */
    public static function getNamespace()
    {
        $var = self::config('namespace');

        if (!is_null($var)) {
            $namespace = array_pop($var);
            if (!is_null($namespace)) {
                return $namespace;
            }
        }

        return "App";
    }

    /**
     * Bootstraps Strata by creating an instance and
     * saving it to the global scope.
     * @param  \Composer\Autoload\ClassLoader Current project's Composer autoloader.
     * @return \Strata\Strata The current application
     */
    public static function bootstrap(ClassLoader $loader)
    {
        $app = new \Strata\Strata();

        // Expose the app context to the current process.
        $GLOBALS['__Strata__'] = $app;

        $app->setLoader($loader);
        $app->run();

        return $app;
    }

    /**
     * Loads strata.php, cleans up the data and saves it as config to the current instance of the Strata object.
     * @return  array The configuration array
     */
    public static function parseProjectConfigFile()
    {
        $configFile = self::getProjectConfigurationFilePath();
        if (file_exists($configFile)) {
            $strata = include_once($configFile);
            if (isset($strata) && count($strata)) {
                return Hash::normalize($strata);
            }
        }
    }

    /**
     * Write the config object to the project's configuration file.
     * @param   array $config The new configuration array
     * @return  bool True is successful
     */
    public static function writeProjectConfigFile($config)
    {
        $configFile = self::getProjectConfigurationFilePath();
        return file_put_contents($configFile, print_r($config, true));
    }

    public static function requireVendorAutoload()
    {
        return require \Strata\Strata::getVendorPath() . 'autoload.php';
    }

    /**
     * Returns the root path of the project.
     * @return string Path
     */
    public static function getRootPath()
    {
        if (defined('ABSPATH')) {
            return dirname(dirname(ABSPATH));
        }
        return getcwd();
    }

    /**
     * Returns the Wordpress themes path of the project.
     * @return string Path
     */
    public static function getThemesPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "web", "app", "themes")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the src folder.
     * @return string Path
     */
    public static function getSRCPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "src")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the db folder.
     * @return string Path
     */
    public static function getDbPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "db")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the utility folder.
     * @return string Path
     */
    public static function getUtilityPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getOurVendorPath(), "src", "Utility")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the config folder.
     * @return string Path
     */
    public static function getConfigurationPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "config")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the project's vendor folder.
     * @return string Path
     */
    public static function getVendorPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "vendor")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to our vendor folder.
     * @return string Path
     */
    public static function getOurVendorPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getVendorPath(), "francoisfaubert", "wordpress-mvc")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to Strata's configuration file.
     * @return string Path
     */
    public static function getProjectConfigurationFilePath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getConfigurationPath(), 'strata.php'));
    }
}
