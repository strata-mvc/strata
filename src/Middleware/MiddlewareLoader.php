<?php

namespace Strata\Middleware;

use Composer\Autoload\ClassLoader;

/**
 * Allows bridging between Strata and its middlewares.
 */
class MiddlewareLoader {

    private $classLoader;
    private $middlewares = array();

    public function __construct(ClassLoader $loader)
    {
        if (is_null($loader)) {
            throw new Exception("MiddlewareLoader requires Composer's ClassLoader object.");
        }

        $this->classLoader = $loader;
        $this->findAvailableMiddlewares();
    }

    /**
     * Specifies if a number of middlewares have been defined.
     * @return boolean True if some are defined.
     */
    public function hasMiddlewares()
    {
        return count($this->middlewares) > 0;
    }

    public function initialize()
    {
        if ($this->hasMiddlewares()) {
            foreach ($this->middlewares as $middleware) {
                $middleware->initialize();
            }
        }
    }

    private function findAvailableMiddlewares()
    {
        foreach($this->classLoader->getPrefixesPsr4() as $prefix => $path) {
            if (preg_match("/^Strata\\\\Middleware\\\\(.+?)\\\\$/", $prefix)) {
                $className = $prefix . "Initializer";
                if (class_exists($className)) {
                    $this->middlewares[] = new $className();
                }
            }
        }
    }
}
