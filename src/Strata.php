<?php
namespace Strata;

use Strata\Logger\Logger;
use Strata\Router\Router;
use Strata\Utility\Hash;

use Strata\Core\StrataContext;
use Strata\Core\StrataConfigurableTrait;

use Strata\Model\CustomPostType\CustomPostTypeLoader;
use Strata\Middleware\MiddlewareLoader;
use Strata\Security\Security;

use Composer\Autoload\ClassLoader;
use Exception;

/**
 * Running Strata instance
 *
 * @package Strata
 * @link    http://strata.francoisfaubert.com/docs/
 */
class Strata extends StrataContext
{

    use StrataConfigurableTrait;

    /**
     * @var bool Specifies the requirements have been met from the current configuration
     */
    protected $ready = false;

    /**
     * @var \Composer\Autoload\ClassLoader Keeps a reference to the current project's Composer autoloader file.
     */
    protected $loader = null;

    protected $logger = null;
    private $middlewareLoader = null;

    public $i18n = null;
    public $router = null;

    /**
     * Prepares the object for its run.
     * @throws \Exception Throws an exception if the configuration array could not be loaded.
     */
    public function init()
    {
        $this->ready = false;

        $this->configureLogger();
        $this->includeUtils();
        $this->setDefaultNamespace();
        $this->configureRouter();

        $this->loadConfiguration();
        $this->addProjectNamespaces();
        $this->setTimeZone();
        $this->localize();

        $this->ready = true;
    }

    /**
     * Kickstarts the router and preload the custom post types associated to the project.
     */
    public function run()
    {
        if (!$this->ready) {
            $this->init();
        }
        $this->configureCustomPostType();
        $this->addAppRoutes();
        $this->loadMiddleware();

        $this->improveSecurity();
    }

    public function loadConfiguration()
    {
        $this->saveConfigurationFileSettings(self::parseProjectConfigFile());

        if (!$this->containsConfigurations()) {
            throw new Exception("Using the Strata bootstraper requires a file named 'config/strata.php' that declares a configuration array named \$strata.");
        }
    }

    /**
     * Assigns a class loader to the application
     * @param ClassLoader $loader The application's class loader.
     */
    public function setLoader(ClassLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Returns a class loader to the application
     * @return ClassLoader $loader The application's class loader.
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Looks through all the packages in composer and if they
     * are in our Strata\Middleware namespace, we try to autoload them.
     */
    protected function loadMiddleware()
    {
        $this->middlewareLoader = new MiddlewareLoader($this->getLoader());
        $this->middlewareLoader->initialize();
    }

    public function getMiddlewares()
    {
        if (!is_null($this->middlewareLoader)) {
            return $this->middlewareLoader->getMiddlewares();
        }

        return array();
    }

    /**
     * Loads up a fake Wordpress wrapper on which the tests will register things.
     * Will only include the file under CLI mode.
     */
    public function includeWordpressFixture()
    {
        if (self::isCommandLineInterface()) {
            include_once(self::getTestPath() . 'Fixture/Wordpress/bootstrap.php');
        }
    }

    public function takeOverWPCLIArgs()
    {
        if (self::isCommandLineInterface()) {
            $wpcliArgs = $_SERVER['argv'];
            if (strstr($wpcliArgs[0], 'wp-cli')) {
                // The first 5 items are wpcli arguments that are needed
                // for WP to exist in the context of the command line.
                $GLOBALS['_SERVER']['argv'] = array_slice($wpcliArgs, 5);
            }

            if (!array_key_exists('SERVER_NAME', $GLOBALS['_SERVER'])) {
                $GLOBALS['_SERVER']['SERVER_NAME'] = "localhost";
            }
        }
    }


    /**
     * Loads up a fake gettext wrapper against which the tests will test.
     * Will only include the file under CLI mode.
     */
    public function includeGettextFixture()
    {
        if (self::isCommandLineInterface()) {
            include_once(self::getTestPath() . 'Fixture/Gettext/bootstrap.php');
        }
    }

    protected function configureLogger()
    {
        $this->logger = new Logger();
    }

    public function log($message, $context = "[Strata]")
    {
        if (!is_null($this->logger)) {
            return $this->logger->log($message, $context);
        }
    }

    protected function localize()
    {
        $this->i18n = new I18n\I18n();
        $this->i18n->initialize();
    }

    /**
     * Configures the default router object to ensure that URL mapping
     * is automated.
     * @return null
     */
    protected function configureRouter()
    {
        $this->router = Router::urlRouting();
    }

    protected function addAppRoutes()
    {
        $routes = $this->getConfig('routes');
        if (is_array($routes)) {
            $this->router->addRoutes($routes);
        }
    }

    /**
     * Configures the post type loader
     * is automated.
     * @return null
     */
    protected function configureCustomPostType()
    {
        $loader = new CustomPostTypeLoader();
        $loader->configure((array)$this->getConfig('custom-post-types'));
        $loader->load();
    }

    /**
     * Saves an array of options to the configuration attribute.
     * @param  array $values A list of project values.
     * @return null
     */
    protected function saveConfigurationFileSettings($values = array())
    {
        if (count($values) > 0) {
            $this->configure($values);
            $this->normalizeConfiguration();
        }
    }

    /**
     * Includes the debugger classes.
     * @return boolean True if the file was correctly included.
     */
    protected function includeUtils()
    {
        return $this->saveCurrentPID() && $this->includeDebug();
    }

    /**
     * Save the latest PHP process ID as a temp file in case an infinite loop
     * or any unexpected error breaks the server, but doesn't close it.
     */
    protected function saveCurrentPID()
    {
        $pid = getmypid();

        if (!self::isCommandLineInterface()) {
            $this->log("", sprintf("[Strata] Loaded and running with process ID %s", $pid));
        }

        $filename = self::getTmpPath() . "pid";
        return @file_put_contents($filename, $pid);
    }

    protected function includeDebug()
    {
        $debug = self::getUtilityPath() . "Debug.php";

        if (file_exists($debug)) {
            return include_once $debug;
        }
    }

    /**
     * Adds the current project namespace to the project's class loader.
     * @return null
     */
    public function addProjectNamespaces()
    {
        $this->loader->setPsr4(self::getNamespace() . "\\", self::getSRCPath());
    }

    public function setDefaultNamespace()
    {
        $this->loader->setPsr4(self::getDefaultNamespace() . "\\", self::getSRCPath());
        $this->loader->setPsr4(self::getDefaultTestNamespace() . "\\", self::getTestPath());
    }

    protected function setTimeZone()
    {
        $timezone = Strata::app()->getConfig("timezone");
        if (is_null($timezone)) {
            $timezone = 'America/New_York';
        }
        date_default_timezone_set($timezone);
    }

    protected function improveSecurity()
    {
        $security = new Security();
        $security->addMesures();
    }
}
