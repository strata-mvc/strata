<?php
/**
 */
namespace MVC\Shell;

class Shell
{
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
        $this->out("");
        if (file_exists(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . ".vagrant")) {
            $this->out('Starting the VM. This can take a few seconds.');
        } else {
            $this->out('A new virtual machine needs to be downloaded and/or setup for the first time. This will take a long time.');
        }
        $this->out("");

        system("vagrant up");
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
       // eventually do something with the args.
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
