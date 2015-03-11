<?php
namespace MVC;

use MVC\Context\RoutingContext;

/**
 * Maps wordpress urls to MVC classes
 *
 * @package       MVC.Router
 * @link          http://wordpress-mvc.francoisfaubert.com/docs/routes/
 */
class Router extends RoutingContext {

    /**
     * @var AltoRouter Instanciated route parser
     */
    protected $_altoRouter = null;

    /**
     * Launches the router instance
     */
    public function init()
    {
        $parsedAltoRoutes = $this->_parseRoutesForAlto(\MVC\Mvc::config('routes'));

        // Create the AltoRouter instance
        $this->_altoRouter = new \AltoRouter();
        $this->_altoRouter->addRoutes($parsedAltoRoutes);
    }

    /**
     * The callback sent to Wordpress' 'init' action. It understands the current
     * url context and calls the current controller's method, if applicable.
     */
    public function onWordpressInit()
    {
        $match = $this->_altoRouter->match();

        if ($match) {
            // Decompose request params to kick off the autoloader.
            $target = explode("#", $match['target']);
            $className = \MVC\Mvc::getNamespace() . "\\Controller\\" . $target[0];

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

        // @bug : This does not seem to work as intended. Wordpress never accepts
        // our dynamic slugs. The concept could probably be thrown out because custom slugs
        // must be handled at the custom post type level.
        foreach ($parsedAltoRoutes as $idx => $route) {
            if (is_array($route[1]) && count($route[1]) === 1) {
                $altoRegex = key($route[1]);
                $wordpressRegex = str_replace('[', '(', $altoRegex);
                $wordpressRegex = ltrim($wordpressRegex, '/');
                $wordpressRegex = preg_replace('/[\*]/', '.*', $wordpressRegex);
                $wordpressRegex = preg_replace('/(:.+?\]\/?)/', ')', $wordpressRegex);

                add_rewrite_rule($wordpressRegex . '$', array_pop($route[1]),'top');
                $parsedAltoRoutes[$idx][1] = $altoRegex;
            }
        }
        return $parsedAltoRoutes;
    }
}

