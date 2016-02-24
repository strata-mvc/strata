<?php

namespace Strata\Shell\Command\Registrar;

use Strata\Strata;
use Symfony\Component\Console\Application;

/**
 * Registers shell commands that may have been added by
 * middlewares loaded by the application.
 */
class MiddlewareCommandRegistrar
{
    /**
     * A link to a shell application to which
     * the commands will be added.
     * @var Application
     */
    private $application = null;

    function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Assigns all the declared shell commands of the middlewares.
     */
    public function assign()
    {
        $app = Strata::app();
        foreach ($app->getMiddlewares() as $middleware) {
            $this->attemptMultipleRegistrations($middleware->shellCommands);
        }
    }

    /**
     * Attempts to register an array of commands
     * @param  array $commandPaths
     */
    private function attemptMultipleRegistrations($commandPaths)
    {
        if (is_array($commandPaths) && count($commandPaths)) {
            foreach ($commandPaths as $path) {
                $this->attemptRegistration($path);
            }
        }
    }

    /**
     * Attempts the registration of a single shell command.
     * @param  string $path
     */
    private function attemptRegistration($path)
    {
        try {
            if (class_exists($path)) {
                $this->application->add(new $path());
            }
        } catch (Exception $e) {
            echo "Unable to autoload the '$path' command.";
        }
    }
}
