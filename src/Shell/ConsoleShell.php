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
            $this->out('Unable to load Boris\Boris.');
            $this->out('');
            $this->out('Make sure you have installed boris as a dependency,');
            $this->out('and that Boris\Boris is registered in your autoloader.');
            $this->out('');
            $this->out('If you are using composer run');
            $this->out('');
            $this->out('$ php composer.phar require d11wtq/boris');
            $this->out('');
            return 1;
        }
        if (!function_exists('pcntl_signal')) {
            $this->out('No process control functions.');
            $this->out('');
            $this->out('You are missing the pcntl extension, the interactive console requires this extension.');
            return 2;
        }
        
        $this->out('You can exit with <info>CTRL-D</info>');
        
        $boris = new Boris('roots > ');
        $boris->start();
    }
    
}