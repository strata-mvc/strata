<?php
namespace Strata\Router\RouteParser;

use Strata\Strata;
use Exception;

/**
 * A route is an object that can be mapped out to a MVC request.
 */
abstract class Route
{
    private $executionStart = 0;

    /**
     * @var Strata\Controller\Controller An instance of the controller linked to the route
     */
    public $controller = null;


    /**
     * @var string The action name
     */
    public $action = null;

    /**
     * @var array A list of arguments to pass through to the controller's action
     */
    public $arguments = array();

    /**
     * This is the entry point of all routers. The inheriting classes will handle
     * how they handle the management of their route type from this function.
     * @throws Exception When it is not implemented by inheriting classes.
     * @return null
     */
    abstract public function process();

    /**
     * Adds a mixed type of possibility against which the route will be validating during the process() step.
     * @param mixed $routes Any type of data that is useful in the case of the class.
     * @return  null
     */
    abstract function addPossibilities(array $routes);

    /**
     * Verifies that the current values can be ran by the router.
     * @return boolean True is the route is considered working and valid.
     */
    public function isValid()
    {
        return !is_null($this->controller) && method_exists($this->controller, $this->action);
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

    protected function logRouteStart()
    {
        $this->executionStart = microtime(true);
        $this->log(sprintf("Routing to -> %s#%s", get_class($this->controller), $this->action));
    }

    protected function logRouteCompletion()
    {
        $executionTime = microtime(true) - $this->executionStart;
        $this->log(sprintf("Done in %s seconds", round($executionTime, 4)));
    }

    protected function log($msg, $type = "[Strata::Router]")
    {
        $app = Strata::app();
        $app->log($msg, $type);
    }
}
