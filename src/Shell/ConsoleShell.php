<?php

namespace MVC\Shell;

use Boris\Boris;
use MVC\Shell\Shell;

/**
 * Simple console wrapper around Boris.
 */
class ConsoleShell extends Shell
{
    /**
     * Start the shell and interactive console.
     *
     * @return int|void
     */
    public function main()
    {
        if (!class_exists('Boris\Boris')) {            
            echo 'Unable to load Boris\Boris.';
            echo '';
            echo 'Make sure you have installed boris as a dependency,';
            echo 'and that Boris\Boris is registered in your autoloader.';
            echo '';
            echo 'If you are using composer run';
            echo '';
            echo '$ php composer.phar require d11wtq/boris';
            echo '';
            return 1;
        }
        if (!function_exists('pcntl_signal')) {
            echo 'No process control functions.';
            echo '';
            echo 'You are missing the pcntl extension, the interactive console requires this extension.';
            return 2;
        }
        echo 'You can exit with <info>CTRL-D</info>';
        
        $boris = new Boris('roots > ');
        $boris->start();
    }
    /**
     * Display help for this console.
     *
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = new ConsoleOptionParser('console', false);
        $parser->description(
            'This shell provides a REPL that you can use to interact ' .
            'with your application in an interactive fashion. You can use ' .
            'it to run adhoc queries with your models, or experiment ' .
            'and explore the features of CakePHP and your application.' .
            "\n\n" .
            'You will need to have boris installed for this Shell to work. ' .
            'Boris is known to not work well on windows due to dependencies on ' .
            'readline and posix.'
        );
        return $parser;
    }
}