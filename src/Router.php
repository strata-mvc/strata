<?php
namespace MVC;

class Router {

    protected $_altoRouter = null;
    protected $_app = null;
    protected $_parsedRoutes = array();

    public static function __callStatic($method, $args)
    {
        if (preg_match('/^___dynamic___callback___(.+)___(.+)/', $method, $matches)) {
            $app = \MVC\Mvc::app();
            $className = $app->getNamespace() . "\\Controllers\\" . $matches[1] . "Controller";
            Router::performAction($className, $matches[2], $args);
        }
    }

    public static function kickstart($app)
    {
        if (array_key_exists('routes', $app->config)) {
            $router = new self();
            $router->assignApp($app);
            add_action('init' , array($router, "onWordpressInit"));
        }
    }

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

    public function assignApp($app)
    {
        $this->_app = $app;
        $this->_altoRouter = new \AltoRouter();
        $this->_altoRouter->addRoutes($this->_parseRoutesForAlto($app->config['routes']));
    }

    public function onWordpressInit()
    {
        $match = $this->_altoRouter->match();
        //debug($match);

        if ($match) {
            // Decompose request params to kick off the autoloader.
            $target = explode("#", $match['target']);

            $className = $this->_app->getNamespace() . "\\Controllers\\" . $target[0];

            if(class_exists($className)) {
                // When a method is passed on, load that method
                if (count($target) > 1) {
                    $methodName = $target[1];
                    Router::performAction($className, $methodName, $match['params']);

                // Also check for the page argument if the page is in the admin
                } elseif (is_admin() && method_exists($className, $_GET['page'])) {
                    Router::performAction($className, $_GET['page']);

                // When no method is sent, gues from the action and page parameters, based on context.
                } elseif (method_exists($className, $_POST['action'])) {
                    Router::performAction($className, $_POST['action']);
                }

                // Register dynaminc shortcodes hooks linked to the instanciated controller
                if (count($ctrl->shortcodes) > 0) {
                    foreach ($ctrl->shortcodes as $shortcode => $methodName) {
                        if(method_exists($ctrl, $methodName)) {
                            add_shortcode($shortcode, array($ctrl, $methodName));
                        }
                    }
                }
            }
        }
    }

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

