<?php

namespace Strata;

use Strata\Logger\LoggerBase;
use Strata\Router\Router;
use Strata\Router\Rewriter;
use Strata\Utility\Hash;
use Strata\Core\StrataContext;
use Strata\Core\StrataConfigurableTrait;
use Strata\Model\CustomPostType\CustomPostTypeLoader;
use Strata\Middleware\MiddlewareLoader;
use Strata\Security\Security;
use Strata\I18n\I18n;
use Strata\Error\BaseErrorHandler;
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
    protected $ready = null;

    /**
     * @var ClassLoader A reference to the current project's Composer autoloader file.
     */
    protected $loader = null;

    /**
     * @var MiddlewareLoader A reference to the middleware manager.
     */
    private $middlewareLoader = null;

    /**
     * @var I18n A reference to the localizations manager.
     */
    public $i18n = null;

    /**
     * @var Router A reference to the active routing object.
     */
    public $router = null;

    /**
     * @var Rewriter A reference to the global rewriter object.
     */
    public $rewriter = null;


    /**
     * Prepares the Strata object for running.
     * @throws Exception Throws an exception if the configuration array could not be loaded.
     */
    public function init()
    {
        if (is_null($this->ready)) {
            $this->ready = false;

            $this->configureLoggers();

            $this->includeUtils();
            $this->setDefaultNamespace();
            $this->setupUrlRouting();

            $this->loadConfiguration();
            $this->addProjectNamespaces();
            $this->localize();

            $this->ready = true;
        }
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

        $this->displayRuntimeHeader();
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
    protected function configureLoggers()
    {
        $loggers = array();
        foreach ($this->extractConfig('logging') as $name => $config) {
            $logger = LoggerBase::factory($name);
            $logger->configure((array)$config);
            $logger->initialize();
            $logKey = isset($config['name']) ? $config['name'] : $name;
            $loggers[$logKey] = $logger;
        }

        if (Strata::isBundledServer()) {
            $logger = LoggerBase::factory('Console');
            $logger->initialize();
            $loggers['StrataConsole'] = $logger;
        }

        $this->setConfig('runtime.loggers', $loggers);
    }

    /**
     * Sends a message to the active logger
     * @param  string $message
     * @param  string $context (Optional)
     */
    public function log($message, $context = "[Strata]")
    {
        $logger = $this->getLogger();
        if (!is_null($logger)) {
            $logger->log($message, $context);
        }
    }

    /**
     * Returns a reference to the Logger. When no $name is
     * provided, attempts to return the most plausible one.
     * @param $name The logger's name
     * @return Logger
     */
    public function getLogger($name = '')
    {
        if (empty($name)) {
            if (self::isBundledServer()) {
                return $this->getConfig("runtime.loggers.StrataConsole");
            }

            if (self::isDev()) {
                return $this->getConfig("runtime.loggers.StrataFile");
            }
        }

        return $this->getConfig("runtime.loggers.$name");
    }

    protected function displayRuntimeHeader()
    {
        $logger = $this->getLogger();

        if (self::isCommandLineInterface()) {
            // tbd
        } elseif (self::isBundledServer()) {
            $logger->nl();
            $logger->log(sprintf(
                "<yellow>Loaded as PID</yellow> <success>#%d</success> <yellow>with</yellow> <success>%d</success> <yellow>handled custom post types.</yellow>",
                $this->getConfig("runtime.pid"),
                count($this->getConfig("runtime.custom_post_types"))
            ), "<info>Strata</info>");
            $logger->log($this->getConfig("runtime.timezone"), "<info>Strata</info>");
        } else {
            // tbd
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
    protected function setupUrlRouting()
    {
        $this->rewriter = new Rewriter();
        $this->rewriter->initialize();

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
        return $this->saveCurrentPID() && $this->includeToolset();
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
        $this->setConfig('runtime.pid', $pid);
        $filename = self::getLogPath() . "pid.log";

        return @file_put_contents($filename, $pid);
    }

    public function registerErrorHandler()
    {
        $handler = new BaseErrorHandler();
        $handler->register();
    }

    /**
     * Includes the global toolset functions in the current scope and
     * returns whether the include was successful or not.
     * @return boolean
     */
    protected function includeToolset()
    {
        $toolset = self::getUtilityPath() . "Toolset.php";

        if (file_exists($toolset)) {
            return include_once $toolset;
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
