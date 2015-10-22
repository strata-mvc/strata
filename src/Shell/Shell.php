<?php
namespace Strata\Shell;

use Symfony\Component\Console\Application;

use Strata\Strata;
use Strata\Shell\Command\Registrar\ProjectCommandRegistrar;
use Strata\Shell\Command\Registrar\MiddlewareCommandRegistrar;

/**
 * A factory that build the Strata command line application.
 */
class Shell
{
    /**
     * Returns a application allowing for known commands.
     * @todo  Account for commands to be loaded by the project namespace (ex: for cron commands).
     * @return Symfony\Component\Console\Application The command line application
     */
    public static function getApplication()
    {
        $application = new Application('Strata Console Application', '0.1.0');

        $registrar = new ProjectCommandRegistrar($application);
        $registrar->assign();

        $registrar = new MiddlewareCommandRegistrar($application);
        $registrar->assign();

        $application->add(new \Strata\Shell\Command\ServerCommand());
        $application->add(new \Strata\Shell\Command\GenerateCommand());
        $application->add(new \Strata\Shell\Command\DBCommand());
        $application->add(new \Strata\Shell\Command\DocumentationCommand());
        $application->add(new \Strata\Shell\Command\EnvCommand());
        $application->add(new \Strata\Shell\Command\TestCommand());
        $application->add(new \Strata\Shell\Command\I18nCommand());

        return $application;
    }

    /**
     * Generates an application and runs it in one swift swoop.
     * @return null;
     */
    public static function run()
    {
        Strata::app()->includeWordpress();

        $shell = self::getApplication();
        $shell->run();
    }
}
