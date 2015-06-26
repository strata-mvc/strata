<?php
namespace Strata\Shell;

// Use our own set of dependencies.
$ourVendor = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
if (file_exists($ourVendor)) {
    require $ourVendor;
}


use Symfony\Component\Console\Application;
use Strata\Shell\Command\Registrar\ProjectCommandRegistrar;

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
        require(\Strata\Strata::getConfigurationPath() . "application.php");

        $application = new Application('Strata Console Application', '0.1.0');

        $registrar = new ProjectCommandRegistrar($application);
        $registrar->assign();

        $application->add(new \Strata\Shell\Command\ServerCommand());
        $application->add(new \Strata\Shell\Command\GenerateCommand());
        $application->add(new \Strata\Shell\Command\DBCommand());
        $application->add(new \Strata\Shell\Command\DocumentationCommand());
        $application->add(new \Strata\Shell\Command\EnvCommand());
        $application->add(new \Strata\Shell\Command\TestCommand());

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
