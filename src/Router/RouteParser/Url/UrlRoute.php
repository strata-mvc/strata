<?php

namespace Strata\Router\RouteParser\Url;

use Strata\Router\RouteParser\Route;
use Strata\Controller\Controller;
use Strata\Controller\Request;
use Strata\Model\WordpressEntity;
use Strata\Utility\Inflector;
use Strata\Strata;
use AltoRouter;
use Exception;

/**
 * Handles routes generated from REST requests.
 */
class UrlRoute extends Route
{
    /**
     * @var string The hidden key for dynamically defined callback functions.
     */
    const DYNAMIC_PARSE = "__strata_dynamic_parse__";

    /**
     * Altorouter is the library that does the heavy lifting for us.
     * @var AltoRouter
     */
    private $altoRouter = null;

    /**
     * @var array Routes defined by the current application's configuration (3rd priority)
     */
    private $applicationRoutes = array();

    /**
     * @var array Routes defined by the current application's model (2nd priority)
     */
    private $modelRoutes = array();

    /**
     * @var array Routes defined automatically by Strata (1st priority)
     */
    private $automatedRoutes = array();

    /**
     * Configures the routing deamon from the routes that have
     * been set during bootstrap.
     */
    public function listen()
    {
        $this->altoRouter = new AltoRouter();
        $this->altoRouter->addRoutes($this->listRegisteredRoutes());
    }

    public function listRegisteredRoutes()
    {
        return array_merge(
            $this->modelRoutes,
            $this->automatedRoutes,
            $this->applicationRoutes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addPossibilities(array $routes)
    {
        foreach ($routes as $route) {
            $this->applicationRoutes[] = $this->parseRouteConfiguration($route);
        }
    }

    /**
     * Adds a resourced based route possibility based on a custom
     * post type or taxonomy.
     * @param WordpressEntity $model
     */
    public function addResourcePossibility(WordpressEntity $model)
    {
        $slug = null;

        if (property_exists($model, "routed") && is_array($model->routed) &&  array_key_exists("controller", $model->routed)) {
            $controllerName = $model->routed['controller'];
            $controllerObject = new $controllerName();
            $controller = $controllerObject->getShortName();
        } else {
            $controller = Controller::generateClassName($model->getShortName());
        }

        $i18n = Strata::i18n();
        if ($i18n->isLocalized()) {
            $currentLocale = $i18n->getCurrentLocale();
            if ($currentLocale && !$currentLocale->isDefault()) {
                $slugInfo = $model->extractConfig("i18n." . $currentLocale->getCode() . ".rewrite.slug");
                $slug = array_pop($slugInfo);

                if (!is_null($slug)) {
                    $this->automatedRoutes[] = array('GET|POST|PATCH|PUT|DELETE', "/$slug/page/[i:pageNumber]/", "$controller#index");
                    $this->automatedRoutes[] = array('GET|POST|PATCH|PUT|DELETE', "/$slug/[:slug]/", "$controller#show");
                    $this->automatedRoutes[] = array('GET|POST|PATCH|PUT|DELETE', "/$slug/?", "$controller#index");
                }
            }
        }

        $slugInfo = $model->extractConfig("rewrite.slug");
        $slug = array_pop($slugInfo);

        if (is_null($slug)) {
            $slug = $model->getWordpressKey();
        }

        $this->automatedRoutes[] = array('GET|POST|PATCH|PUT|DELETE', "/$slug/page/[i:pageNumber]/", "$controller#index");
        $this->automatedRoutes[] = array('GET|POST|PATCH|PUT|DELETE', "/$slug/[:slug]/", "$controller#show");
        $this->automatedRoutes[] = array('GET|POST|PATCH|PUT|DELETE', "/$slug/?", "$controller#index");
    }

    public function addModelPossibilities(array $routes)
    {
        foreach ($routes as $route) {
            $this->modelRoutes[] = $this->parseRouteConfiguration($route);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->logRouteStart();
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        $this->assignViewVars();
        $this->logRouteCompletion();
    }

    /**
     * This function is required to send variables in Wordpress' scope.
     * Unlike the templating using Controller#view->render() which allow
     * passing variables, Wordpress's load_template extracts variables in
     * $wp_query only.
     */
    protected function assignViewVars()
    {
        global $wp_query;

        if (!is_null($this->controller) && !is_null($this->controller->view)) {
            foreach ($this->controller->view->getVariables() as $key => $value) {
                $wp_query->set($key, $value);
                $GLOBALS[$key] = $value;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process($url = null)
    {
        $match = $this->altoRouter->match($url);

        if (!is_array($match)) {
            return;
        }

        if (is_404() && $match["target"] === self::DYNAMIC_PARSE) {
            $match = array(
                'target' => 'PageController',
                'params' => [
                    'action' => 'notFound'
                ],
                'name' => null
            );
        }

        $this->handleRouterAnswer($match);
    }

    /**
     * Adds a new route configuration.
     * @param array $route
     */
    private function parseRouteConfiguration($route)
    {
        if (!is_array($route)) {
            throw new Exception("Strata configuration file contains an invalid route.");
        }

        return $this->isDynamic($route) ?
            $this->parseDynamicRoute($route) :
            $this->parseMatchedRoute($route);
    }

    /**
     * Defines whether the $route is dynamic based
     * on it's length
     * @param  array  $route
     * @return boolean
     */
    private function isDynamic($route)
    {
        return count($route) < 3;
    }

    /**
     * Adds a new dynamic route
     * @param array $route
     */
    private function parseDynamicRoute($route)
    {
        return array($route[0], $route[1], self::DYNAMIC_PARSE);
    }

    /**
     * Adds a matched route
     * @param array $route
     */
    private function parseMatchedRoute($route)
    {
        return array($route[0], $route[1], $route[2]);
    }

    /**
     * Analized the route match to see if it's a dynamic or
     * matched route.
     * @param  array $match An AltoRouter answer
     */
    private function handleRouterAnswer($match)
    {
        if ($match["target"] === self::DYNAMIC_PARSE) {
            $this->handleDynamicRouterAnswer($match);
        } else {
            $this->handleMatchedRouterAnswer($match);
        }

        if (!method_exists($this->controller, $this->action)) {
            $this->action = "noActionMatch";
        }
    }

    /**
     * Handles dynamic routes
     * @param  array $match An AltoRouter answer
     */
    private function handleDynamicRouterAnswer($match)
    {
        $this->controller   = $this->getControllerFromDynamicMatch($match);
        $this->action       = $this->getActionFromDynamicMatch($match);
        $this->arguments    = $this->getArgumentsFromDynamicMatch($match);
    }


    /**
     * Handles matched routes
     * @param  array $match An AltoRouter answer
     */
    private function handleMatchedRouterAnswer($match)
    {
        $this->controller   = $this->getControllerFromMatch($match);
        $this->action       = $this->getActionFromMatch($match);
        $this->arguments    = $this->getArgumentsFromMatch($match);
    }

    /**
     * Gets a Controller instance form the matched information
     * @param  array $match An AltoRouter answer
     * @return Controller A controller object
     */
    private function getControllerFromMatch($match = array())
    {
        try {
            $target = explode("#", $match["target"]);
            return Controller::factory($target[0]);
        } catch (Exception $e) {
            // The controller did not exist, we don't care at this point.
            // We'll just ignore the route.
        }

        return Controller::factory("App");
    }

    /**
     * Gets a Controller instance form the dynamic information
     * @param  array $match An AltoRouter answer
     * @return Controller A controller object
     */
    private function getControllerFromDynamicMatch($match = array())
    {
        try {
            if (array_key_exists("controller", $match["params"])) {
                return Controller::factory($match["params"]["controller"]);
            }
        } catch (Exception $e) {
            // The controller did not exist, we don't care at this point.
            // We'll just ignore the route.
        }

        return Controller::factory("App");
    }

    /**
     * Gets the action from the AltoRouter match.
     * @param  array $match An AltoRouter answer
     * @return string
     */
    private function getActionFromMatch($match = array())
    {
        $target = explode("#", $match["target"]);

        if (count($target) > 1) {
            return $target[1];
        }

        if (array_key_exists("action", $match["params"])) {
            return $this->getActionFromParam($match["params"]["action"]);
        }

        $this->controller->request = new Request();
        if (is_admin() && $this->controller->request->hasGet('page')) {
            return $this->controller->request->get('page');
            // When no method is sent, guesstimate from the action post value (ex: basic ajax)
        } elseif ($this->controller->request->hasPost('action')) {
            return $this->controller->request->post('action');
        }
    }

    /**
     * Gets the action from the dynamic match.
     * @param  array $match An AltoRouter answer
     * @return string
     */
    private function getActionFromDynamicMatch($match)
    {
        if (array_key_exists("action", $match["params"])) {
            return $this->getActionFromParam($match["params"]["action"]);
        }

        if (array_key_exists("controller", $match["params"]) && !array_key_exists("action", $match["params"])) {
            return "index";
        }

        return "noActionMatch";
    }

    /**
     * Formats the action based on the router's match
     * @param  string $action
     * @return string
     */
    private function getActionFromParam($action)
    {
        $action = str_replace("-", "_", $action);
        if (substr($action, -1) === "/") {
            $action = substr($action, 0, -1);
        }

        return lcfirst(Inflector::camelize($action));
    }

    /**
     * Gets the possible additional arguments from the route match.
     * @param  array $match An AltoRouter answer
     * @return array
     */
    private function getArgumentsFromMatch($match = array())
    {
        if (is_array($match['params']) && count($match['params'])) {
            $params = $match['params'];
            return is_array($params) ? $params : array($params);
        }

        return array();
    }

    /**
     * Gets the possible additional arguments from the dynamic match.
     * @param  array $match An AltoRouter answer
     * @return array
     */
    private function getArgumentsFromDynamicMatch($match = array())
    {
        if (array_key_exists("params", $match["params"])) {
            $params = $match["params"]["params"];
            return is_array($params) ? $params : array($params);
        }

        return array();
    }
}
