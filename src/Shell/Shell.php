<?php
/**
 */
namespace MVC\Shell;

class Shell
{
    public static function factory($key)
    {
        \MVC\Mvc::loadEnvConfiguration();

        if (!defined("MVC_APP_NAMESPACE")) {
            throw new Exception("'MVC_APP_NAMESPACE' must be set.");
        }

        switch($key) {
            case "c" : case "console"   : return new \MVC\Shell\ConsoleShell();
            case "s" : case "server"    : return new \MVC\Shell\ServerShell();
            case "g" : case "generate"  : return new \MVC\Shell\GenerateShell();
            case "m" : case "migrate"   : return new \MVC\Shell\MigrationShell();
        }
    }

    public function initialize($options = array())
    {

    }

    /**
     * Starts up the Shell and displays the welcome message.
     * Allows for checking and configuring prior to command or main execution
     *
     * Override this method if you want to remove the welcome information,
     * or otherwise modify the pre-command flow.
     *
     * @return void
     */
    public function startup()
    {

    }

    public function shutdown()
    {

    }

    public function getPHPBin()
    {
        return PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
    }

    public function out($msg)
    {
        echo $msg . "\n";
    }

    /**
     *
     * @return void
     */
    public function contextualize($args)
    {
       // The extending classes will be able to do
       // something with the arguments sent to the shell command.
    }
}
