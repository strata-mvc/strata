<?php
namespace Strata\Router\RouteParser\Alto;

use Strata\Router\Router;
use Strata\Router\RouteParser\Alto\AltoRoute;

/**
 * Maps wordpress urls to Strata classes
 *
 * @package       Strata.Router
 * @link          http://strata.francoisfaubert.com/docs/routes/
 */
class AltoRouteParser extends Router {

    public static function factory($routes = array())
    {
        $router = new self();
        if (count($routes)) {
            $router->addRoutes($routes);
        }
        return $router;
    }

    private $registered = false;

    function __construct()
    {
        $this->route = new AltoRoute();
    }

    /**
     * Configures the router instance
     */
    public function addRoutes($routes = array())
    {
        if (!$this->isRegistered()) {
            $this->registerWordpressAction();
        }

        $this->route->addPossibilities($routes);
    }

    /**
     * This function is required to send variables in Wordpress' scope.
     * Unlike the templating using Controller#view->render() which allow
     * passing variables, Wordpress's load_template extracts variables in
     * $wp_query only.
     */
    public function assignViewVars()
    {
        global $wp_query;

        if (!is_null($this->route->controller) && !is_null($this->route->controller->view)) {
            foreach ($this->route->controller->view->getVariables() as $key => $value) {
                if (array_key_exists($key, $wp_query->query_vars)) {
                    error_log(sprintf("[STRATA] : Wordpress has already reserved the view variable %s.", $key));
                } else {
                    $wp_query->set($key, $value);
                }
            }
        }
    }

    protected function registerWordpressAction()
    {
        if (Router::isAjax() || is_admin()) {
            add_action('init', array($this, "onWordpressEarlyInit"));
        } else {
            add_action('wp', array($this, "onWordpressInit"));
        }

        $this->registered = true;
    }

    protected function isRegistered()
    {
        return $this->registered;
    }

    /**
     * The callback sent to Wordpress' 'init' action. It understands the current
     * url context and calls the current controller's method, if applicable.
     */
    public function onWordpressInit()
    {
        $this->run();
        $this->assignViewVars();
    }

    public function onWordpressEarlyInit()
    {
        $this->onWordpressInit();
    }
}
