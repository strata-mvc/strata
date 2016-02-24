<?php
namespace Strata\Router\RouteParser\Callback;

use Strata\Controller\Controller;
use Strata\Router\RouteParser\Route;

class CallbackRoute extends Route
{
    /**
     * {@inheritdoc}
     */
    public function addPossibilities(array $route)
    {
        $this->controller = Controller::factory($route[0]);
        $this->action = $route[1];
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {

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
        $this->logRouteCompletion();
    }
}
