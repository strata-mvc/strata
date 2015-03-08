<?php
/**
 */
namespace MVC\Shell;

class Shell
{
    
    /**
     * An instance of ConsoleOptionParser that has been configured for this class.
     *
     * @var \Cake\Console\ConsoleOptionParser
     */
    public $OptionParser;
    
    /**
     * Contains command switches parsed from the command line.
     *
     * @var array
     */
    public $params = [];

    
    /**
     * The command (method/task) that is being run.
     *
     * @var string
     */
    public $command;

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
    
    public function out($msg)
    {
        echo $msg . "\n";
    }
    
    public function getOptionParser()
    {
        
    }
    
    public function runCommand($argv, $autoMethod = false)
    {
        $command = isset($argv[0]) ? $argv[0] : null;
        $this->OptionParser = $this->getOptionParser();
        try {
            list($this->params, $this->args) = $this->OptionParser->parse($argv);
        } catch (ConsoleException $e) {
            $this->out('<error>Error: ' . $e->getMessage() . '</error>');
            $this->out($this->OptionParser->help($command));
            return false;
        }
                        
        $this->command = $command;
        if (!empty($this->params['help'])) {
            return $this->_displayHelp($command);
        }
        $subcommands = $this->OptionParser->subcommands();
        $method = Inflector::camelize($command);
        $isMethod = $this->hasMethod($method);
        if ($isMethod && $autoMethod && count($subcommands) === 0) {
            array_shift($this->args);
            $this->startup();
            return call_user_func_array([$this, $method], $this->args);
        }
        if ($isMethod && isset($subcommands[$command])) {
            $this->startup();
            return call_user_func_array([$this, $method], $this->args);
        }
        if ($this->hasTask($command) && isset($subcommands[$command])) {
            $this->startup();
            array_shift($argv);
            return $this->{$method}->runCommand($argv, false);
        }
        if ($this->hasMethod('main')) {
            $this->startup();
            return call_user_func_array([$this, 'main'], $this->args);
        }
        $this->out($this->OptionParser->help($command));
        return false;
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
