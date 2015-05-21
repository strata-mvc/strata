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
            $router->registerWordpressAction();
        }
        return $router;
    }

    function __construct()
    {
        $this->route = new AltoRoute();
    }

    /**
     * Configures the router instance
     */
    public function addRoutes($routes = array())
    {
        $this->route->addPossibilities($routes);
    }

    /**
     * This function is required to send variables in Wordpress' scope.
     * Unlike the templating using Controller#view->render() which allow
     * passing variables, Wordpress's load_template extracts variables in
     * $wp_query only.
     * @param   $wp_query
     */
    public function assignViewVars($wp_query)
    {
        if (!is_null($this->route->controller->view)) {
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
        add_action('init' , array($this, "onWordpressInit"));
        add_action('parse_query',  array($this, 'assignViewVars'));
    }

    /**
     * The callback sent to Wordpress' 'init' action. It understands the current
     * url context and calls the current controller's method, if applicable.
     */
    public function onWordpressInit()
    {
        $this->run();
    }

}


