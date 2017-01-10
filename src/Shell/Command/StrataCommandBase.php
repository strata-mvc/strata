<?php

namespace Strata\Shell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

use Strata\Strata;

/**
 * Base class for Shell Command reflection.
 * This class contains a basic toolset to perform repetitive visual outputs.
 * It is also the interface between Strata and Symfony's codebase.
 */
class StrataCommandBase extends Command
{
    /**
     *
     * @param  string $name The class name of the controller
     * @return mixed       A controller
     */
    public static function factory($name)
    {
        $classpath = StrataCommandNamer::generateClassPath($name);

        if (class_exists($classpath)) {
            return new $classpath();
        }

        throw new Exception("[Strata] No file matched the command '$classpath'.");
    }

    /**
     * Generates a possible namespace and classname combination of a
     * Strata controller. Mainly used to avoid hardcoding the '\\Shell\\Command\\'
     * string everywhere.
     * @param  string $name The class name of the controller
     * @return string       A fulle namespaced controller name
     */
    public static function generateClassPath($name)
    {
        return StrataCommandNamer::generateClassName($name);
    }

    /**
     * A tree representation prefix.
     *
     * @var string
     */
    protected $tree_line = "  ├── ";

    /**
     * The bottom part of a tree representation prefix.
     *
     * @var string
     */
    protected $tree_end = "  └── ";

    /**
     * A reference to the current input interface object
     *
     * @var InputInterface
     */
    public $input = null;

    /**
     * A reference to the current output interface object
     *
     * @var OutputInterface
     */
    public $output = null;

    /**
     * Creates a visual representation of a tree branch. This is useful when
     * generating a list of files.
     * @param  boolean $isEnd Specifies if we are at the end of a list
     * @return string         The correct characters based on the <code>$isEnd</code> context.
     */
    public function tree($isEnd = false)
    {
        return $isEnd ? $this->tree_end : $this->tree_line;
    }

    /**
     * Creates a visual representation of an OK status. This is useful when performing
     * an action that can fail or be skipped.
     * @param  string $msg The optional message associated to the status.
     * @return string      A colored string
     */
    public function ok($msg = "")
    {
        return "<info>[ OK ]</info> " . $msg;
    }

    /**
     * Creates a visual representation of a skipped status. This is useful when performing
     * an action that can fail or succeed.
     * @param  string $msg The optional message associated to the status.
     * @return string      A colored string
     */
    public function skip($msg = "")
    {
        return "<fg=cyan>[SKIP]</fg=cyan> " . $msg;
    }

    /**
     * Creates a visual representation of a failed status. This is useful when performing
     * an action that can be skipped or succeed.
     * @param  string $msg The optional message associated to the status.
     * @return string      A colored string
     */
    public function fail($msg = "")
    {
        return "<error>[FAIL]</error> " . $msg;
    }

    /**
     * Specifies whether the script is currently running under windows
     * @return boolean
     */
    protected function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === "WIN";
    }

    /**
     * Return a new line.
     * @return string      An empty line break.
     */
    public function nl()
    {
        return $this->output->writeLn('');
    }

    /**
     * The startup function should be called each time a command is being executed. It
     * saves the Input and Output interfaces to allow the command to use it further down
     * the process.
     * @param  InputInterface  $input  The current input interface
     * @param  OutputInterface $output The current output interface
     * @return null
     */
    public function startup(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * The shutdown function should be called each time a command has completed execution.
     * @return null
     */
    public function shutdown()
    {

    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        return parent::configure();
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface   $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return parent::execute($input, $output);
    }
}
