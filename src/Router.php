<?php
namespace MVC;

/**
 * Maps wordpress urls to MVC classes
 *
 * @package       MVC.Router
 * @link          http://wordpress-mvc.francoisfaubert.com/docs/routes/
 */
class Router {

    /**
     * @var AltoRouter Instanciated route parser
     */
    protected $_altoRouter = null;

    /**
     * Catch-all for static methods that allows just in time understanding of
     * dynamic callbacks. This is required because function like add_submenu_page
     * do not allow you to send arguments to a callback. (See EntityTable::addAdminMenus)
     * @param string $method
     * @param mixed $args Optional
     */
    public static function __callStatic($method, $args)
    {
        if (preg_match('/^___dynamic___callback___(.+)___(.+)(___\d+)?/', $method, $matches)) {
            $app = \MVC\Mvc::app();
            $className = $app->getNamespace() . "\\Controllers\\" . $matches[1];
            Router::performAction($className, $matches[2], $args);
        }
    }

    /**
     * Generate a dynamic and unique callback ready to use with wordpress' add_action calls.
     * @param string $ctrl Controller class shortname
     * @param string $action
     * @return array A valid callback for call_user_func
     */
    public static function callback($ctrl, $action)
    {
        $count = 0;
        while(method_exists(self, $action))  {
            $action = $action . "___" . $count++;
        }
        return array('MVC\Router', sprintf('___dynamic___callback___%s___%s', $ctrl, $action));
    }

    /**
     * Starts the routing process based on the app's predefined configuration.
     */
    public static function kickstart()
    {
        $app = \MVC\Mvc::app();

        // If routes are not present, router has nothing to do and processing should be ignored.
        if ($app && array_key_exists('routes', $app->config)) {
            $router = new self();
            $router->init();

            // Hook on Wordpress' init action to start any process.
            add_action('init' , array($router, "onWordpressInit"));
        }
    }

    /**
     * Executes a complete calls to the Controller object, including controller callbacks and shortcodes generation.
     */
    public static function performAction($className, $methodName, $params = array())
    {
        if(method_exists($className, $methodName))  {
            $ctrl = new $className();
            $ctrl->init();
            call_user_func(array($ctrl, "before"));
            call_user_func_array(array($ctrl, $methodName), $params);
            call_user_func(array($ctrl, "after"));
        }
    }

    /**
     * Launches the router instance
     */
    public function init()
    {
        $app = \MVC\Mvc::app();
        // Create the AltoRouter instance
        $this->_altoRouter = new \AltoRouter();
        $this->_altoRouter->addRoutes($this->_parseRoutesForAlto($app->config['routes']));
    }

    /**
     * The callback sent to Wordpress' 'init' action. It understands the current
     * url context and calls the current controller's method, if applicable.
     */
    public function onWordpressInit()
    {
        $match = $this->_altoRouter->match();
        $app = \MVC\Mvc::app();

        if ($match) {
            // Decompose request params to kick off the autoloader.
            $target = explode("#", $match['target']);
            $className = $app->getNamespace() . "\\Controllers\\" . $target[0];

            if(class_exists($className)) {
                // When a method is passed on, load that method
                if (count($target) > 1) {
                    $methodName = $target[1];
                    Router::performAction($className, $methodName, $match['params']);

                // Also check for the page argument if the page is in the admin
                } elseif (is_admin() && method_exists($className, $_GET['page'])) {
                    Router::performAction($className, $_GET['page']);

                // When no method is sent, guess from the action value
                } elseif (method_exists($className, $_POST['action'])) {
                    Router::performAction($className, $_POST['action']);
                }
            }
        }
    }

    /**
     * Wordpress route regexes are different from those Alto can parse.
     * Convert the alto regexes to a pattern add_rewrite_rule can use.
     * @param array $config The configuration array of app->config
     * @return array The parsed routes
     */
    protected function _parseRoutesForAlto($config)
    {
        $parsedAltoRoutes = $config;
        foreach ($parsedAltoRoutes as $idx => $route) {
            if (is_array($route[1]) && count($route[1]) === 1) {
                $altoRegex = key($route[1]);
                $wordpressRegex = str_replace('[', '(', $altoRegex);
                $wordpressRegex = ltrim($wordpressRegex, '/');
                $wordpressRegex = preg_replace('/[\*]/', '.*', $wordpressRegex);
                $wordpressRegex = preg_replace('/(:.+?\]\/?)/', ')', $wordpressRegex);

                add_rewrite_rule($wordpressRegex . '/?$', array_pop($route[1]),'top');
                $parsedAltoRoutes[$idx][1] = $altoRegex;
            }
        }
        return $parsedAltoRoutes;
    }
}

