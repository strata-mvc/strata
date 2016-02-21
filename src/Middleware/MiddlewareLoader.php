<?php

namespace Strata\Middleware;

use Composer\Autoload\ClassLoader;
use Exception;

/**
 * Allows bridging between Strata and its middlewares.
 * Middlewares must be loaded in Composer's json file and must
 * contain a initializer using the following namespace as
 * convention:
 *
 * <?php
 *  namespace Strata\Middleware\Mycompany_MyclassInitializer {
 *
 *  }
 */
class MiddlewareLoader
{
    /**
     * @var ClassLoader Composer's class loader
     */
    private $classLoader;

    /**
     * @var array A list of middlewares to load
     */
    private $middlewares = array();

    /**
     * Middleware loader constructor builds a list of middleware configurations
     * and instantiates them.
     * @param ClassLoader $loader
     */
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

    /**
     * Goes through the list of defined middlewares to attempt
     * to initialize them.
     */
    public function initialize()
    {
        if ($this->hasMiddlewares()) {
            foreach ($this->middlewares as $middleware) {
                $middleware->initialize();
            }
        }
    }

    /**
     * Returns the list of defined middlewares
     * @return array
     */
    public function getMiddlewares()
    {
        return (array)$this->middlewares;
    }

    /**
     * Goes through the class loader and finds packages
     * defined as a possible Strata Middleware.
     * @return array A list of instantiated Middlewares
     */
    private function findAvailableMiddlewares()
    {
        foreach ($this->classLoader->getPrefixesPsr4() as $prefix => $path) {
            if (preg_match("/^Strata\\\\Middleware\\\\(.+?)\\\\$/", $prefix)) {
                $className = $prefix . "Initializer";
                if (class_exists($className)) {
                    $this->middlewares[] = new $className();
                }
            }
        }
    }
}
