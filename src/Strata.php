<?php
namespace Strata;

use Strata\Router\Router;
use Strata\Utility\Hash;
use Strata\Utility\ErrorMessenger;
use Strata\Context\StrataContext;
use Strata\Model\CustomPostType\CustomPostTypeLoader;

use Composer\Autoload\ClassLoader;
use Exception;

// Use our own set of dependencies.
$ourVendor = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($ourVendor)) {
    require $ourVendor;
}

/**
 * Running Strata instance
 *
 * @package       Strata
 * @link          http://strata.francoisfaubert.com/docs/
 */
class Strata extends StrataContext {
    /**
     * @var array The configuration array specified in the theme's app.php
     */
    protected $_config = array();

    /**
     * @var bool Specifies the requirements have been met from the current configuration
     */
    protected $_ready = false;

    /**
     * @var \Composer\Autoload\ClassLoader Keeps a reference to the current project's Composer autoloader file.
     */
    protected $_loader = null;

    /**
     * Prepares the object for its run.
     * @throws \Exception Throws an exception if the configuration array could not be loaded.
     */
    public function init()
    {
        $this->_ready = false;

        $this->_includeUtils();
        $this->loadConfiguration();

        $this->_ready = true;
    }

    /**
     * Kickstarts the router and preload the custom post types associated to the project.
     */
    public function run()
    {
        if (!$this->_ready) {
            $this->init();
        }

        $this->_configureCustomPostType();
        $this->_configureRouter();
        $this->_initializeStrataMuPlugins();
    }

    public function loadConfiguration()
    {
        $this->_saveConfigValues(self::parseProjectConfigFile());

        if (is_null($this->_config)) {
            throw new Exception("Using the Strata bootstraper requires a file named 'config/strata.php' that declares a configuration array named \$strata.");
        }

        $this->_addProjectNamespace();
    }


    /**
     * Assigns a class loader to the application
     * @param ClassLoader $loader The application's class loader.
     */
    public function setLoader(ClassLoader $loader)
    {
        $this->_loader = $loader;
    }

    /**
     * Returns a class loader to the application
     * @return ClassLoader $loader The application's class loader.
     */
    public function getLoader()
    {
        return $this->_loader;
    }

    /**
     * Fetches a value in the app's configuration array for the duration of the runtime.
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function getConfig($key)
    {
        return Hash::extract($this->_config, $key);
    }

    /**
     * Saves a value in the app's configuration array for the duration of the runtime.
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function setConfig($key, $value)
    {
        return Hash::set($this->_config, $key, $value);
    }

    /**
     * Looks through all the packages in composer and if they
     * are in our Strata\MuPlugin namespace, we try to autoload them.
     */
    protected function _initializeStrataMuPlugins()
    {
        $loader = $this->getLoader();
        foreach($loader->getPrefixesPsr4() as $prefix => $path) {
            if (strstr($prefix, "Strata\\MuPlugin")) {
                $className = $prefix . "PluginInitializer";
                if (class_exists($className)) {
                    $initializer = new $className();
                    $initializer::initialize();
                }
            }
        }
    }

    /**
     * Configures the default router object to ensure that URL mapping
     * is automated.
     * @return null
     */
    protected function _configureRouter()
    {
        Router::automateURLRoutes($this->getConfig('routes'));
    }

    /**
     * Configures the post type loader
     * is automated.
     * @return null
     */
    protected function _configureCustomPostType()
    {
        $loader = new CustomPostTypeLoader($this->getConfig('custom-post-types'));
        $loader->load();
    }

    /**
     * Saves an array of options to the _config attribute.
     * @param  array $values A list of project values.
     * @return null
     */
    protected function _saveConfigValues($values = array())
    {
        if (count($values) > 0) {
            $this->_config = Hash::normalize($values);
        }
    }


    /**
     * Includes the debugger classes.
     * @return boolean True if the file was correctly included.
     */
    protected function _includeUtils()
    {
        return include_once(self::getUtilityPath() . "Debug.php");
    }

    /**
     * Adds the current project namespace to the projet's class loader.
     * @return null
     */
    protected function _addProjectNamespace()
    {
        $srcPath = self::getSRCPath();
        $namespace = self::getNamespace() . "\\";

        $this->_loader->setPsr4($namespace, $srcPath);
    }
}
