<?php
namespace Strata\Shell\Command\Registrar;

use Strata\Strata;
use Strata\Shell\Command\StrataCommand;

class MiddlewareCommandRegistrar
{
    private $application = null;

    function __construct(\Symfony\Component\Console\Application $application)
    {
        $this->application = $application;
    }

    public function assign()
    {
        $app = Strata::app();
        foreach ($app->getMiddlewares() as $middleware) {
            $this->attemptMultipleRegistrations($middleware->shellCommands);
        }
    }

    private function attemptMultipleRegistrations($commandPaths)
    {
        if (count($commandPaths)) {
            foreach ($commandPaths as $path) {
                $this->attemptRegistration($path);
            }
        }
    }

    private function attemptRegistration($path)
    {
        try {
            if (class_exists($path)) {
                $this->application->add(new $path());
            }
        } catch(Exception $e) {
            echo "Unable to autoload the '$path' command.";
        }
    }
}
