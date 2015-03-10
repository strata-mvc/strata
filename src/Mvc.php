<?php
namespace MVC;

use MVC\CustomPostTypes;
use MVC\Router;
use MVC\Utility\Hash;

/**
 * Running MVC instance
 *
 * @package       MVC.Router
 * @link          http://wordpress-mvc.francoisfaubert.com/docs/routes/
 */
class Mvc {


    /**
     * @var array The configuration array specified in the theme's app.php
     */
    public $config = null;

    /**
     * @var bool Specifies the requirements have been met from the current configuration
     */
    protected $_ready = false;


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
        return array_pop(Hash::extract($app->config, $key));
    }

    public static function loadEnvConfiguration()
    {
        $ini = parse_ini_file(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "production.ini");
        foreach($ini as $key => $value) {
            if (!defined($key)) {
                define(strtoupper($key), $value);
            }
        }
    }

    public static function bootstrap()
    {
        $app = new \MVC\Mvc();
        $app->init();

        if ($app->ready()) {
            // Add the project's directory to the autoloader
            \MVC\Mvc::$loader->setPsr4($app->config['key'] . "\\", MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "src");

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
        return \MVC\Mvc::$loader->addPsr4($key, $path);
    }

    /**
     * Prepares the object for its run.
     */
    public function init()
    {
        $this->_ready = false;

        $this->_parseConfigFile();

        if (is_null($this->config)) {
            trigger_error("Using the MVC bootstraper requires a file named [theme]/wordpress-mvc/app.php that declares a configuration array named \$app." , E_USER_WARNING);
            return;
        }
        elseif (!array_key_exists('key', $this->config)) {
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
        if ($this->_ready) {

            // Expose the app context to the current process.
            $GLOBALS['__MVC__'] = $this;

            // Set up the creation of custom post types based on models
            if (array_key_exists('custom-post-types', $this->config)) {
                CustomPostTypes\Loader::preload();
            }

            Router::kickstart();
        }
    }

    /**
     * Return the current project's namespace key
     */
    public function getNamespace()
    {
        return ucfirst($this->config['key']);
    }

    /**
     * Return the ready state of the app
     */
    public function ready()
    {
        return $this->_ready;
    }

    /**
     * Loads app.php, cleans up the data and saves it as config to the current instance of the MVC object.
     */
    protected function _parseConfigFile()
    {
        $configFile = MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . 'app.php';
        if (file_exists($configFile)) {
            include_once($configFile);
            if(isset($app) && count($app)) {
                $this->config = Hash::normalize($app);
            }
            unset($app);
        }
    }
}
