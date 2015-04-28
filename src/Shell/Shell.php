<?php
namespace Strata\Shell;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

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

        $application->add(new \Strata\Shell\ServerCommand());
        $application->add(new \Strata\Shell\GenerateCommand());
        $application->add(new \Strata\Shell\DBCommand());
        $application->add(new \Strata\Shell\DocumentationCommand());
        $application->add(new \Strata\Shell\EnvCommand());

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
