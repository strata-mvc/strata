<?php
namespace MVC;

use MVC\Router;
use MVC\Utility\Hash;
use MVC\Context\MvcContext;
use MVC\Model\CustomPostType\Loader;

/**
 * Running MVC instance
 *
 * @package       MVC
 * @link          http://wordpress-mvc.francoisfaubert.com/docs/
 */
class Mvc extends MvcContext {
    /**
     * @var array The configuration array specified in the theme's app.php
     */
    protected $_config = null;

    /**
     * @var bool Specifies the requirements have been met from the current configuration
     */
    protected $_ready = false;

    /**
     * Prepares the object for its run.
     */
    public function init()
    {
        $this->_ready = false;

        $this->_includeUtils();
        $this->_parseProjectConfigFile();

        if (is_null($this->_config)) {
            trigger_error("Using the MVC bootstraper requires a file named [theme]/wordpress-mvc/app.php that declares a configuration array named \$app." , E_USER_WARNING);
            return;
        }
        elseif (!array_key_exists('key', $this->_config)) {
            trigger_error("Using the MVC bootstraper requires a config value called 'key' that sets the main project namespace." , E_USER_WARNING);
            return;
        }

        $this->_ready = true;
    }

    /**
     * Kickstarts the router and preload the custom post types associated to the project.
     */
    public function run()
    {
        if (!$this->_ready) {
            trigger_error("The MVC instance is not ready to be called. Have you initiated it?" , E_USER_WARNING);
            return;
        }

        // Set up the creation of custom post types based on models
        if (array_key_exists('custom-post-types', $this->_config)) {
            Loader::preload();
        }

        Router::kickstart();
    }

    /**
     * Return the ready state of the app
     */
    public function ready()
    {
        return (bool)$this->_ready;
    }

    /**
     * Fetches a value in the app's configuration array
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function read($key)
    {
        return Hash::extract($this->_config, $key);
    }

    public function write($key, $value)
    {
        return Hash::set($this->_config, $key, $value);
    }

    /**
     * Loads app.php, cleans up the data and saves it as config to the current instance of the MVC object.
     */
    protected function _parseProjectConfigFile()
    {
        $configFile = self::getProjectConfigurationFilePath();
        if (file_exists($configFile)) {
            include_once($configFile);
            if(isset($app) && count($app)) {
                $this->_config = Hash::normalize($app);
            }
            unset($app);
        }
    }

    protected function _includeUtils()
    {
        include_once(self::getUtilityPath() . "Debug.php");
    }
}
