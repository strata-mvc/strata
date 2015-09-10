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

    protected $cancelled = false;

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

    abstract function start();

    abstract function end();

    /**
     * Verifies that the current values can be ran by the router.
     * @return boolean True is the route is considered working and valid.
     */
    public function isValid()
    {
        return !is_null($this->controller) && method_exists($this->controller, $this->action);
    }

    public function cancel()
    {
        $this->cancelled = true;
        $this->logRouteCancellation();
    }

    public function isCancelled()
    {
        return $this->cancelled;
    }

    protected function logRouteCancellation()
    {
        $this->log(sprintf("[Cancel] Routing to -> %s#%s", get_class($this->controller), $this->action));
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
