<?php

namespace Strata\Router\RouteParser;

use Strata\Strata;
use Exception;

/**
 * A route is an object that can be mapped out to a MVC request.
 */
abstract class Route
{
    /**
     * @var integer Used to store the time when the request has started
     */
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
     * @var boolean Flag to store the current status of the route.
     */
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

    /**
     * Starts the handling of the route.
     */
    abstract function start();

    /**
     * Ends the handling of the route.
     */
    abstract function end();

    /**
     * Verifies that the current values can be ran by the router.
     * @return boolean True is the route is considered working and valid.
     */
    public function isValid()
    {
        return !is_null($this->controller) && method_exists($this->controller, $this->action);
    }

    /**
     * Cancels the current route process.
     */
    public function cancel()
    {
        $this->cancelled = true;
        $this->logRouteCancellation();
    }

    /**
     * Specifies whether the current route has been canceled.
     * @return boolean
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * Logs a message explaining how the route was canceled.
     */
    protected function logRouteCancellation()
    {
        $message = sprintf("<warning>[Cancel]</warning> <info>Routing to -> %s#%s</info>", get_class($this->controller), $this->action);

        $logger = Strata::app()->getLogger();
        $logger->log($message, "<green>Strata:Route</green>");
    }

    /**
     * Logs a message warning a routing process had begun.
     */
    protected function logRouteStart()
    {
        $this->executionStart = microtime(true);
        $message = sprintf("<info>Routing to -> %s#%s</info>", get_class($this->controller), $this->action);

        $logger = Strata::app()->getLogger();
        $logger->logNewContext($message, "<green>Strata:Route</green>", "green");
    }

    /**
     * Logs a message warning a routing process had end.
     */
    protected function logRouteCompletion()
    {
        $executionTime = microtime(true) - $this->executionStart;

        $logger = Strata::app()->getLogger();
        $logger->logContextEnd(sprintf("Done in %s seconds", round($executionTime, 4)), "<green>Strata:Route</green>");
    }
}
