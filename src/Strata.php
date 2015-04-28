<?php
namespace Strata;

use Strata\Router;
use Strata\Utility\Hash;
use Strata\Context\StrataContext;
use Strata\Model\CustomPostType\Loader;

// Use our own set of dependencies.
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

/**
 * Running Strata instance
 *
 * @package       Strata
 * @link          http://wordpress-mvc.francoisfaubert.com/docs/
 */
class Strata extends StrataContext {
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
            trigger_error("Using the Strata bootstraper requires a file named 'config/strata.php' that declares a configuration array named \$strata." , E_USER_WARNING);
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
            trigger_error("The Strata instance is not ready to be called. Have you initiated it?" , E_USER_WARNING);
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
     * Loads app.php, cleans up the data and saves it as config to the current instance of the Strata object.
     */
    protected function _parseProjectConfigFile()
    {
        $configFile = self::getProjectConfigurationFilePath();
        if (file_exists($configFile)) {
            $strata = include_once($configFile);
            if(isset($strata) && count($strata)) {
                $this->_config = Hash::normalize($strata);
            }
        }
    }

    protected function _includeUtils()
    {
        include_once(self::getUtilityPath() . "Debug.php");
    }
}
