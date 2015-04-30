<?php
namespace Strata\Router\RouteParser\Callback;

use Strata\Controller\Controller;
use Strata\Router\RouteParser\Route;

class CallbackRoute extends Route
{
    CONST CALLBACK_REGEX = '/^___dynamic___callback___(.+)___(.+)(___\d+)?/';

    private $_methodPattern = null;

    /**
     * {@inheritdoc}
     */
    public function addPossibilities($route)
    {
        $this->_methodPattern = $route;
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
        $match = $this->_parseMethodPattern();

        $this->controller = $this->_getControllerFromMatch($match);
        $this->action = $this->_getActionFromMatch($match);
    }

    private function _getControllerFromMatch($match = array())
    {
        return Controller::factory($match[1]);
    }

    private function _getActionFromMatch($match = array())
    {
        if (count($match) > 2) {
            return $match[2];
        }
    }

    private function _parseMethodPattern()
    {
        if (preg_match(self::CALLBACK_REGEX, $this->_methodPattern, $matches)) {
            return $matches;
        }
    }
}
