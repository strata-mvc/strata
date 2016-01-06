<?php
namespace Strata\Router\RouteParser\Alto;

use AltoRouter;
use Strata\Router\RouteParser\Route;

use Strata\Controller\Controller;
use Strata\Controller\Request;
use Strata\Model\CustomPostType\CustomPostType;

use Strata\Utility\Hash;
use Strata\Utility\Inflector;

use Exception;

class AltoRoute extends Route
{
    /**
     * Altorouter is the library that does the heavy lifting for us.
     * @var AltoRouter
     */
    private $altoRouter = null;

    const DYNAMIC_PARSE = "__strata_dynamic_parse__";

    public function __construct()
    {
        $this->altoRouter = new AltoRouter();
    }

    /**
     * {@inheritdoc}
     */
    public function addPossibilities(array $routes)
    {
        foreach ($routes as $route) {
            $this->addRouteConfig($route);
        }
    }

    public function addResource(CustomPostType $customPostType)
    {
        $slug = $customPostType->hasConfig("rewrite.slug")
            ? $customPostType->getConfig("rewrite.slug")
            : $customPostType->getWordpressKey();

        $controller = Controller::generateClassName($customPostType->getShortName());

        $this->addMatchedRoute(array('GET|POST|PATCH|PUT|DELETE', "/$slug/?", "$controller#index"));
        $this->addMatchedRoute(array('GET|POST|PATCH|PUT|DELETE', "/$slug/page/[.*]/?", "$controller#index"));
        $this->addMatchedRoute(array('GET|POST|PATCH|PUT|DELETE', "/$slug/[.*]/?", "$controller#show"));
    }

    public function start()
    {
        $this->logRouteStart();
    }

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
                if (array_key_exists($key, $wp_query->query_vars)) {
                    error_log(sprintf("[STRATA] : Wordpress has already reserved the view variable %s.", $key));
                } else {
                    $wp_query->set($key, $value);

                    // I don't think the following is actually necessary.
                    $GLOBALS[$key] = $value;
                }
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

        $this->handleRouterAnswer($match);
    }

    private function addRouteConfig($route)
    {
        if (!is_array($route)) {
            throw new Exception("Strata configuration file contains an invalid route.");
        }

        if ($this->isDynamic($route)) {
            $this->addDynamicRoute($route);
        } else {
            $this->addMatchedRoute($route);
        }
    }

    private function isDynamic($route)
    {
        return count($route) < 3;
    }

    private function addDynamicRoute($route)
    {
        $this->addMatchedRoute(array($route[0], $route[1], self::DYNAMIC_PARSE));
    }

    private function addMatchedRoute($route)
    {
        $route = $this->patchBuiltInServerPrefix($route);
        $this->altoRouter->map($route[0], $route[1], $route[2]);
    }

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

    private function handleDynamicRouterAnswer($match)
    {
        $this->controller   = $this->getControllerFromDynamicMatch($match);
        $this->action       = $this->getActionFromDynamicMatch($match);
        $this->arguments    = $this->getArgumentsFromDynamicMatch($match);
    }

    private function handleMatchedRouterAnswer($match)
    {
        $this->controller   = $this->getControllerFromMatch($match);
        $this->action       = $this->getActionFromMatch($match);
        $this->arguments    = $this->getArgumentsFromMatch($match);
    }

    private function getControllerFromMatch($match = array())
    {
        $target = explode("#", $match["target"]);
        return Controller::factory($target[0]);
    }

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

    private function getActionFromMatch($match = array())
    {
        $target = explode("#", $match["target"]);

        if (count($target) > 1) {
            return $target[1];
        }

        $this->controller->request = new Request();
        if (is_admin() && $this->controller->request->hasGet('page')) {
            return $this->controller->request->get('page');
            // When no method is sent, guesstimate from the action post value (basic ajax)
        } elseif ($this->controller->request->hasPost('action')) {
            return $this->controller->request->post('action');
        }
    }

    private function getActionFromDynamicMatch($match)
    {
        if (array_key_exists("action", $match["params"])) {
            $action = $match["params"]["action"];
            $action = str_replace("-", "_", $action);
            return lcfirst(Inflector::camelize($action));
        }

        if (array_key_exists("controller", $match["params"]) && !array_key_exists("action", $match["params"])) {
            return "index";
        }

        return "noActionMatch";
    }

    private function getArgumentsFromMatch($match = array())
    {
        if (is_array($match['params']) && count($match['params'])) {
            $params = $match['params'];
            return is_array($params) ? $params : array($params);
        }

        return array();
    }

    private function getArgumentsFromDynamicMatch($match = array())
    {
        if (array_key_exists("params", $match["params"])) {
            $params = $match["params"]["params"];
            return is_array($params) ? $params : array($params);
        }

        return array();
    }

    /**
     * Built in server will generate links with index.php because
     * it doesn't have access to mod_rewrite. This function appends
     * index to the route sent in.
     */
    private function patchBuiltInServerPrefix($route)
    {
        if (!preg_match("/^\/index.php/i", $route[1])) {
            $route[1] = "(/index.php)?" . $route[1];
        }

        return $route;
    }
}
