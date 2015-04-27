<?php
/**
 */
namespace Strata\Shell;

use Exception;

class Shell
{
    const TREE_LINE = "├── ";
    const TREE_END = "└── ";

    const COLOR_END   = "\033[0m";
    const COLOR_BLACK = "\033[0;30m";
    const COLOR_DARK_GRAY = "\033[1;30m";
    const COLOR_BLUE = "\033[0;34m";
    const COLOR_LIGHT_BLUE = "\033[1;34m";
    const COLOR_GREEN = "\033[0;32m";
    const COLOR_LIGHT_GREEN = "\033[1;32m";
    const COLOR_CYAN = "\033[0;36m";
    const COLOR_LIGHT_CYAN = "\033[1;36m";
    const COLOR_RED = "\033[0;31m";
    const COLOR_LIGHT_RED = "\033[1;31m";
    const COLOR_PURPLE = "\033[0;35m";
    const COLOR_LIGHT_PURPLE = "\033[1;35m";
    const COLOR_BROWN = "\033[0;33m";
    const COLOR_YELLOW = "\033[1;33m";
    const COLOR_LIGHT_GRAY = "\033[0;37m";
    const COLOR_WHITE = "\033[1;37m";

    public static function factory($key)
    {
        \Strata\Strata::loadEnvConfiguration();

        switch($key) {
            case "s" : case "server"    : return new \Strata\Shell\ServerShell();
            case "g" : case "generate"  : return new \Strata\Shell\GenerateShell();
            case "m" : case "migrate"   : return new \Strata\Shell\MigrationShell();
            case "c" : case "console"   : return new \Strata\Shell\ConsoleShell();
            case "doc"                  : return new \Strata\Shell\DocumentationShell();
            case "strata"               : return new \Strata\Shell\StrataShell();
            default                     : throw new Exception("That is not a valid command.");
        }
    }

    public function initialize($options = array())
    {

    }

    public function tree($isEnd = false)
    {
        return $isEnd ? self::TREE_END : self::TREE_LINE;
    }

    public function error($str)
    {
        return self::COLOR_RED . $str . self::COLOR_END;
    }

    public function info($str)
    {
        return self::COLOR_LIGHT_CYAN . $str . self::COLOR_END;
    }

    public function success($str)
    {
        return self::COLOR_GREEN . $str . self::COLOR_END;
    }

    public function ok($msg = "")
    {
        return $this->success('[ OK ] ') . $msg;
    }

    public function skip($msg = "")
    {
        return $this->info('[SKIP] ') . $msg;
    }

    public function fail($msg = "")
    {
        return $this->error('[FAIL] ') . $msg;
    }

    public function nl()
    {
        $this->out("");
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
        echo " " . $msg . "\n";
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
