<?php

namespace Strata\Shell;

use Symfony\Component\Console\Application;
use Strata\Strata;
use Strata\Shell\Command\Registrar\ProjectCommandRegistrar;
use Strata\Shell\Command\Registrar\MiddlewareCommandRegistrar;
use Strata\Shell\Command\Registrar\StrataCommandRegistrar;

/**
 * A factory that build the Strata command line application.
 */
class Shell
{
    /**
     * Returns a application allowing for known commands.
     * @todo  Account for commands to be loaded by the project namespace (ex: for cron commands).
     * @return Application The command line application
     */
    public static function getApplication()
    {
        restore_error_handler();
        restore_exception_handler();

        $application = new Application('Strata Console Application', '0.1.0');

        $registrar = new ProjectCommandRegistrar($application);
        $registrar->assign();

        $registrar = new MiddlewareCommandRegistrar($application);
        $registrar->assign();

        $registrar = new StrataCommandRegistrar($application);
        $registrar->assign();

        return $application;
    }

    /**
     * Generates an application and runs it in one swift swoop.
     * @return null;
     */
    public static function run()
    {
        $shell = self::getApplication();
        $shell->run();
    }
}
