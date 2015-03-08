<?php
/**
 */
namespace MVC\Shell;

class Shell
{
    /**
     * Starts up the Shell and displays the welcome message.
     * Allows for checking and configuring prior to command or main execution
     *
     * Override this method if you want to remove the welcome information,
     * or otherwise modify the pre-command flow.
     *
     * @return void
     * @link http://book.cakephp.org/3.0/en/console-and-shells.html#Cake\Console\ConsoleOptionParser::startup
     */
    public function startup()
    {
        $this->_welcome();
    }
    /**
     * Displays a header for the shell
     *
     * @return void
     */
    protected function _welcome()
    {
        echo "";
        echo "Welcome to Roots Console.";
        echo "";
    }
}
