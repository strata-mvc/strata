<?php
namespace MVC;

class Router {

    protected $_altoRouter = null;
    protected $_app = null;

    public static function kickstart($app)
    {
        if (array_key_exists('routes', $app->config)) {
            foreach ($app->config['routes'] as $route) {

                // If there are dynamic parameters, decompose them and send a simpler version to wordpress
                if (strstr($route[1], "[")) {
                    $wordpressRegex = "([^/]*)";

                    // Replace alto's rules to a more catchall regex so wordpress
                    // doesn't try to do too much with our values.
                    $wordpressFriendlyUrl = substr(preg_replace("/\[.+?\]/", $wordpressRegex, $route[1]), 1);
                    // Get the request's page name without slashes .
                    $pagename = substr($route[1], 1, strpos($route[1], "/", 1)-1);

                    // Though wordpress wouldn't know what to do with the values, send them
                    // along the wordpress way anyway.
                    $i = 0;
                    while ($i++ < (int)substr_count($wordpressFriendlyUrl, $wordpressRegex)) {
                        $pageurl .= "&var".$i.'=$matches['.$i.']';
                    }
                    add_rewrite_rule('^'.trailingslashit($wordpressFriendlyUrl).'?', "index.php?pagename=$pagename",'top');
                }
            }

            flush_rewrite_rules();
            $router = new self();
            $router->assignApp($app);
            $router->assignMap($app->config['routes']);
            add_action('init' , array($router, "onWordpressInit"));
        }
    }

    public function assignApp($app)
    {
        $this->_app = $app;
    }

    public function assignMap($map)
    {
        $this->_altoRouter = new \AltoRouter\AltoRouter();
        $this->_altoRouter->addRoutes($map);
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

                // Expose the app context to the current process.
                Mvc::expose($this->_app);

                $ctrl = new $className();
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
}

