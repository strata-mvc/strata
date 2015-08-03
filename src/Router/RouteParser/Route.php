<?php
namespace Strata\Router\RouteParser;

use Exception;

/**
 * A route is an object that can be mapped out to a MVC request.
 */
abstract class Route
{

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
}
