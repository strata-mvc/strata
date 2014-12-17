<?php
namespace MVC;

use MVC\CustomPostTypes;
use MVC\Router;
use MVC\Utility\Hash;

class Mvc {

    public $config = null;
    public $ready = false;

    /**
     * Because the processing is called asynchronously using wordpress' hooks,
     * we will lose the referece to the kickstarter object.
     * we need to make sure the config values are availlable through global values.
     */
    public static function app()
    {
        return $GLOBALS['__MVC__'];
    }

    public static function config($key)
    {
        $app = Mvc::app();
        return array_pop(Hash::extract($app->config, $key));
    }

    public static function expose($ref)
    {
        return $GLOBALS['__MVC__'] = $ref;
    }

    public function init()
    {
        $this->parseConfigFile();

        if (is_null($this->config)) {
            trigger_error("Using the MVC bootstraper requires a file named [theme]/wordpress-mvc/app.php that declares a configuration array named \$app." , E_USER_WARNING);
            return;
        }
        elseif (!array_key_exists('key', $this->config)) {
            trigger_error("Using the MVC bootstraper requires a config value called 'key' that sets the main project namespace." , E_USER_WARNING);
            return;
        }

        $this->ready = true;
    }

    public function run()
    {
        if ($this->ready) {
            // Set up the creation of custom post types based on models
            if (array_key_exists('custom-post-types', $this->config)) {
                CustomPostTypes\Loader::preload($this);
            }

            // When not in the admin, set up MVC routing.
            if (!is_admin() || defined('DOING_AJAX')) {
                Router::kickstart($this);
            }
        }
    }

    public function getNamespace()
    {
        return ucfirst($this->config['key']);
    }

    public function parseConfigFile()
    {
        // load the config file
        $configFile = get_template_directory() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'wordpress-mvc' . DIRECTORY_SEPARATOR . 'app.php';
        if (file_exists($configFile)) {
            include_once($configFile);
            if(isset($app) && count($app)) {
                $this->config = $app;
            }
            unset($app);
        }
    }
}
