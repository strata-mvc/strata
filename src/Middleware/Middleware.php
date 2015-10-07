<?php

namespace Strata\Middleware;

abstract class Middleware
{

    public $shellCommands = array();

    abstract function initialize();
}
