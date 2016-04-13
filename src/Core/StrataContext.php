<?php

namespace Strata\Core;

use Strata\Strata;
use Strata\Utility\Hash;
use Composer\Autoload\ClassLoader;
use Exception;

/**
 * Contains all the static methods related to the Strata app object.
 */
class StrataContext
{
    const STRATA_KEY = "__Strata__";

    /**
     * Returns the global instantiated Strata object.
     * @return Strata
     */
    public static function app()
    {
        if (array_key_exists(self::STRATA_KEY, $GLOBALS)) {
            return $GLOBALS[self::STRATA_KEY];
        }
    }

    /**
     * Returns the current URL router of the instantiated Strata
     * object.
     * @return \Strata\Router\Router
     */
    public static function router()
    {
        $app = self::app();
        if (!is_null($app)) {
            return $app->router;
        }
    }

    /**
     * Returns the current URL rewriter of the instantiated Strata
     * object.
     * @return \Strata\Router\Rewriter
     */
    public static function rewriter()
    {
        $app = self::app();
        if (!is_null($app)) {
            return $app->rewriter;
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
            return $app->getConfig($key);
        }
    }

    /**
     * Returns the current localization object of the instantiated Strata
     * object.
     * @return \Strata\I18n\I18n
     */
    public static function i18n()
    {
        $app = self::app();
        if (!is_null($app)) {
            return $app->i18n;
        }
    }

    /**
     * Returns the current project's root namespace key
     * @return string
     */
    public static function getNamespace()
    {
        $var = self::config('namespace');
        if (!is_null($var)) {
            return $var;
        }

        return self::getDefaultNamespace();
    }

    /**
     * Returns the default application namespace within Strata
     * @return string
     */
    public static function getDefaultNamespace()
    {
        return "App";
    }

    /**
     * Returns the default namespace for tests within Strata
     * @return String
     */
    public static function getDefaultTestNamespace()
    {
        return "Test";
    }

    /**
     * Bootstraps Strata by creating a Strata instance and
     * saving it to the global scope.
     * @param  ClassLoader Current project's Composer autoloader.
     * @return Strata The current application
     */
    public static function bootstrap(ClassLoader $loader)
    {
        $app = new Strata();
        $app->setLoader($loader);

        // Expose the app context to the current process.
        $GLOBALS[self::STRATA_KEY] = $app;

        return $app;
    }

    /**
     * Loads ~/config/strata.php, cleans up the data and saves it as
     * the active configuration of the current instance of the Strata object.
     * @return  array Configuration array
     */
    public static function parseProjectConfigFile()
    {
        $configFile = self::getProjectConfigurationFilePath();
        if (file_exists($configFile)) {
            $strata = include $configFile;
            if (isset($strata) && count($strata)) {
                return Hash::normalize($strata);
            }
        }
    }

    /**
     * Write the configuration object to the project's
     * configuration file.
     * @param   array $config The new configuration array
     * @return  bool True is successful
     */
    public static function writeProjectConfigFile($config)
    {
        $configFile = self::getProjectConfigurationFilePath();
        return file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
    }

    /**
     * Includes Composer's generator autoloader from the vendor
     * directory
     * @return boolean
     */
    public static function requireVendorAutoload()
    {
        return include Strata::getVendorPath() . 'autoload.php';
    }

    /**
     * Returns whether Strata is running from the
     * CLI tool but not the bundled server.
     * @return boolean
     */
    public static function isCommandLineInterface()
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * Returns whether Strata is running from the
     * command line server
     * @return boolean
     */
    public static function isBundledServer()
    {
        return php_sapi_name() === 'cli-server';
    }

    /**
     * Returns whether Strata is running in development mode.
     * @return boolean
     */
    public static function isDev()
    {
        return !defined("WP_ENV") || WP_ENV == 'development';
    }

    /**
     * Returns whether Strata is running in test mode.
     * @return boolean
     */
    public static function isTest()
    {
        return defined('STRATA_INCLUDED_WORDPRESS_MOCK') && (bool)STRATA_INCLUDED_WORDPRESS_MOCK;
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
     * Returns the Wordpress plugins path of the project.
     * @return string Path
     */
    public static function getPluginsPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "web", "app", "plugins")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the Wordpress must-use plugins path of the project.
     * @return string Path
     */
    public static function getMUPluginsPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "web", "app", "mu-plugins")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the Wordpress path.
     * @return string Path
     */
    public static function getWordpressPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "web", "wp")) . DIRECTORY_SEPARATOR;
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
     * Returns the path to the temporary folder.
     * @return string Path
     */
    public static function getTmpPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "tmp")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the log folder.
     * @return string Path
     */
    public static function getLogPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "log")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the test folder.
     * @return string Path
     */
    public static function getTestPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "test")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the project shell command folder.
     * @return string Path
     */
    public static function getCommandPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getShellPath(), "Command")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the project shell folder.
     * @return string Path
     */
    public static function getShellPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getSRCPath(), "Shell")) . DIRECTORY_SEPARATOR;
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
     * Returns the path to the Middleware folder.
     * @return string Path
     */
    public static function getMiddlewarePath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getSRCPath(), "Middleware")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to the bin folder.
     * @return string Path
     */
    public static function getBinPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getRootPath(), "bin")) . DIRECTORY_SEPARATOR;
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
     * Returns the path to the configuration folder.
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
     * Returns the path to Strata's vendor folder.
     * @return string Path
     */
    public static function getOurVendorPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getVendorPath(), "francoisfaubert", "strata")) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the path to Strata's configuration file.
     * @return string Path
     */
    public static function getProjectConfigurationFilePath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getConfigurationPath(), 'strata.php'));
    }

    /**
     * Returns the path to Strata's locale configurations.
     * @return string Path
     */
    public static function getLocalePath()
    {
        return implode(DIRECTORY_SEPARATOR, array(self::getConfigurationPath(), 'locale')) . DIRECTORY_SEPARATOR;
    }
}
