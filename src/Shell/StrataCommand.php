<?php
/**
 */
namespace Strata\Shell;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

/**
 * Strata self maintaining Shell
 */
class StrataCommand extends Command
{
    const TREE_LINE = "├── ";
    const TREE_END = "└── ";

    protected $_input = null;
    protected $_output = null;

    public function tree($isEnd = false)
    {
        return $isEnd ? self::TREE_END : self::TREE_LINE;
    }

    public function ok($msg = "")
    {
        return "<info>[ OK ]</info> " . $msg;
    }

    public function skip($msg = "")
    {
        return "<fg=cyan>[SKIP]</fg=cyan> " . $msg;
    }

    public function fail($msg = "")
    {
        return "<error>[FAIL]</error> " . $msg;
    }

    public function nl()
    {
        $this->_output->writeLn('');
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
    public function startup(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;
    }

    public function shutdown()
    {

    }
}
