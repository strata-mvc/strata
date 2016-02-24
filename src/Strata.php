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
use Strata\I18n\I18n;
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
     * @var bool Flag specifying the requirements have been met from the current configuration.
     */
    protected $ready = false;

    /**
     * @var ClassLoader A reference to the current project's Composer autoloader file.
     */
    protected $loader = null;

    /**
     * @var Logger A reference to the global Strata logger object.
     */
    protected $logger = null;

    /**
     * @var MiddlewareLoader A reference to the middleware manager.
     */
    private $middlewareLoader = null;

    /**
     * @var I18n A reference to the localizations manager.
     */
    public $i18n = null;

    /**
     * @var Router A ference to the active routing object.
     */
    public $router = null;

    /**
     * Prepares the Strata object for running.
     * @throws Exception Throws an exception if the configuration array could not be loaded.
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
        $this->localize();

        $this->ready = true;
    }

    /**
     * Kickstarts the Router and preload the custom post types associated to the project.
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

    /**
     * Loads the configuration file and assigns it to the running app.
     * @throws Exception An exception is raised when the configuration file cannot
     * be loaded.
     */
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

    /**
     * Returns the list of declared middlewares in the application.
     * @return array
     */
    public function getMiddlewares()
    {
        if (!is_null($this->middlewareLoader)) {
            return $this->middlewareLoader->getMiddlewares();
        }

        return array();
    }

    /**
     * Loads up a Wordpress fixture on which the tests will register filters and
     * actions.
     * The file will only be included under CLI mode.
     */
    public function includeWordpressFixture()
    {
        if (self::isCommandLineInterface()) {
            include_once(self::getTestPath() . 'Fixture/Wordpress/bootstrap.php');
        }
    }

    /**
     * ~/src/Scripts/runner.php and WP-CLI command line scripts do not have the same
     * amount of parameters. This takes over WP-CLI's order of arguments
     * and registers them back for Strata.
     */
    public function takeOverWPCLIArgs()
    {
        if (self::isCommandLineInterface()) {
            $wpcliArgs = $_SERVER['argv'];
            if (strstr($wpcliArgs[0], 'wp-cli')) {
                // The first 4 items are wpcli arguments that are needed
                // for WP to exist in the context of the command line.
                $GLOBALS['_SERVER']['argv'] = array_slice($wpcliArgs, 4);
            }

            if (!array_key_exists('SERVER_NAME', $GLOBALS['_SERVER'])) {
                $GLOBALS['_SERVER']['SERVER_NAME'] = "localhost";
            }
        }
    }

    /**
     * Loads up a gettext fixture against which the tests will test.
     * The file will only be included under CLI mode.
     */
    public function includeGettextFixture()
    {
        if (self::isCommandLineInterface()) {
            include_once(self::getTestPath() . 'Fixture/Gettext/bootstrap.php');
        }
    }

    /**
     * Configures the global Logger instance
     */
    protected function configureLogger()
    {
        $this->logger = new Logger();
    }

    /**
     * Sends a message to the active logger
     * @param  string $message
     * @param  string $context (Optional)
     */
    public function log($message, $context = "[Strata]")
    {
        if (!is_null($this->logger)) {
            $this->logger->log($message, $context);
        }
    }

    /**
     * Initializes the internationalization class
     */
    protected function localize()
    {
        $this->i18n = new I18n();
        $this->i18n->initialize();
    }

    /**
     * Configures the default router object.
     */
    protected function configureRouter()
    {
        $this->router = Router::urlRouting();
    }

    /**
     * Adds the pre-configured routes to the global
     * router.
     */
    protected function addAppRoutes()
    {
        $routes = $this->getConfig('routes');
        if (is_array($routes)) {
            $this->router->addRoutes($routes);
        }
    }

    /**
     * Configures the post type loader
     */
    protected function configureCustomPostType()
    {
        $loader = new CustomPostTypeLoader();
        $loader->configure((array)$this->getConfig('custom-post-types'));
        $loader->load();
    }

    /**
     * Saves an array of configuration attribute to the application.
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
     * or any unexpected error breaks the server and prevents Strata
     * from closing it automatically.
     * @return boolean
     */
    protected function saveCurrentPID()
    {
        $pid = getmypid();

        if (!self::isCommandLineInterface()) {
            $this->log("[Strata]", sprintf("Loaded and running with process ID %s", $pid));
        }

        $filename = self::getTmpPath() . "pid";
        return @file_put_contents($filename, $pid);
    }

    /**
     * Includes the global debug function in the current scope and
     * returns whether the include was successful or not.
     * @return boolean
     */
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

    /**
     * Sets the default namespaces Strata knows about in the project's Composer's
     * ClassLoader.
     */
    public function setDefaultNamespace()
    {
        $this->loader->setPsr4(self::getDefaultNamespace() . "\\", self::getSRCPath());
        $this->loader->setPsr4(self::getDefaultTestNamespace() . "\\", self::getTestPath());
    }

    /**
     * Improves PHP and Wordpress security of the application.
     */
    protected function improveSecurity()
    {
        $security = new Security();
        $security->addMeasures();
    }
}
