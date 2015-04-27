<?php
namespace Strata;

use Strata\Context\RoutingContext;

/**
 * Maps wordpress urls to Strata classes
 *
 * @package       Strata.Router
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
        // Create the AltoRouter instance
        $this->_altoRouter = new \AltoRouter();
        $this->_altoRouter->addRoutes(\Strata\Strata::config('routes'));
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
            $className = \Strata\Strata::getNamespace() . "\\Controller\\" . $target[0];

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
            } else {
                $error = new \Strata\Shell\Shell();
                error_log($error->fail('Strata : No file matched the controller handled by this route. Looked for ' . $error->info($className) . '.'));
            }
        }
    }
}

