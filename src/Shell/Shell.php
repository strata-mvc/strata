<?php

namespace Strata\Shell;

use Symfony\Component\Console\Application;

// We need our own autoload file
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class Shell
{
    public static function getApplication()
    {
        $application = new Application('Strata Console Application', '0.1.0');

        $application->add(new \Strata\Shell\ServerCommand());
        $application->add(new \Strata\Shell\GenerateCommand());
        $application->add(new \Strata\Shell\DBCommand());
        $application->add(new \Strata\Shell\DocumentationCommand());
        $application->add(new \Strata\Shell\StrataAppCommand());

        return $application;
    }


    public static function run()
    {
        $shell = self::getApplication();
        $shell->run();
    }

}
