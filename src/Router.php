<?php
namespace MVC;

class Router {

    protected $_altoRouter = null;
    protected $_app = null;
    protected $_parsedRoutes = array();

    public static function kickstart($app)
    {
        if (array_key_exists('routes', $app->config)) {
            $router = new self();
            $router->assignApp($app);
            add_action('init' , array($router, "onWordpressInit"));
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
            $methodName = $target[1];

            if(class_exists($className) && method_exists($className, $methodName)) {

                $ctrl = new $className();
                $ctrl->init();
                call_user_func(array($ctrl, "before"));
                call_user_func_array(array($ctrl, $methodName), $match['params']);
                call_user_func(array($ctrl, "after"));

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

