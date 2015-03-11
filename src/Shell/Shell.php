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
            case "c" : case "console" : return new \MVC\Shell\ConsoleShell();
            case "s" : case "server" : return new \MVC\Shell\ServerShell();
            case "g" : case "generate" : return new \MVC\Shell\GenerateShell();
            case "m" : case "migrate" : return new \MVC\Shell\MigrationShell();
        }
    }

    public function initialize($options = array())
    {
        $this->_welcome();
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
        $msg = file_exists(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . ".vagrant") ?
            'Starting the VM. This can take a few seconds.' :
            'A new virtual machine needs to be downloaded and/or setup for the first time. This will take a long time.';

        $this->out("");
        $this->out($msg);
        $this->out("");

        system("vagrant up");
    }

    public function shutdown()
    {
        $this->out("");
        $this->out('Halting the VM.');
        $this->out("");

        return system("vagrant halt");
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

    /**
     * Displays a header for the shell
     *
     * @return void
     */
    protected function _welcome()
    {
        $this->out("");
        $this->out("========================================================================");
        $this->out(" Welcome to Wordpress MVC Console");
        $this->out("========================================================================");
        $this->out("");
    }
}
