<?php
namespace Strata\Router\RouteParser\Alto;

use AltoRouter;
use Strata\Controller\Controller;
use Strata\Router\RouteParser\Route;
use Strata\Controller\Request;

class AltoRoute extends Route
{
    /**
     * Altorouter is the library that does the heavy lifting for us.
     * @var AltoRouter
     */
    private $_altoRouter = null;

    public function __construct()
    {
        $this->_altoRouter = new AltoRouter();
    }

    /**
     * {@inheritdoc}
     */
    public function addPossibilities($routes)
    {
        $routes = $this->_patchBuiltInServerPrefix($routes);
        $this->_altoRouter->addRoutes($routes);
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $this->_handleRouterAnswer();
    }

    private function _handleRouterAnswer()
    {
        $match = $this->_altoRouter->match();
        if (!is_array($match)) {
            return;
        }

        $this->controller   = $this->_getControllerFromMatch($match);
        $this->action       = $this->_getActionFromMatch($match);
        $this->arguments    = $this->_getArgumentsFromMatch($match);
    }

    private function _getControllerFromMatch($match = array())
    {
        $target = explode("#", $match["target"]);
        return Controller::factory($target[0]);
    }

    private function _getActionFromMatch($match = array())
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

    private function _getArgumentsFromMatch($match = array())
    {
        if (is_array($match['params']) && count($match['params'])) {
            return $match['params'];
        }

        return array();
    }

    // Built in server will generate links with index.php because
    // it doesn't have access to mod_reqrite
    private function _patchBuiltInServerPrefix($routes)
    {
        foreach ($routes as $idx => $route) {
            if (!preg_match("/^\/index.php/i", $route[1])) {
                $route[1] = "(/index.php)?" . $route[1];
                $routes[$idx] = $route;
            }
        }
        return $routes;
    }
}
