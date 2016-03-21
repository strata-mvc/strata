<?php

use Strata\Strata;
use Strata\Logger\Debugger;

if (!function_exists('debug')) {
    /**
     * Prints out better looking debug information about a variable.
     * This echoes directly where it is called.
     * @param mixed1, [ $mixed2, $mixed3, ...]
     */
    function debug()
    {
        $mixed = func_get_args();
        if (count($mixed) === 1) {
            $mixed = $mixed[0];
        }

        $context = "In unknown context at unknown line";
        foreach (debug_backtrace() as $idx => $file) {
            if ($file['file'] != __FILE__) {
                $last = explode(DIRECTORY_SEPARATOR, $file['file']);
                $context = sprintf("In %s at line %s: ", $last[count($last)-1], $file['line']);
                break;
            }
        }


        foreach (func_get_args() as $variable) {
            $exported = Debugger::export($variable, 5);

            if (Strata::isCommandLineInterface() || Strata::isBundledServer()) {
                Strata::app()->getLogger("StrataConsole")->debug("\n$context\n" . $exported . "\n");
            }

            echo "<div style=\"".Debugger::HTML_STYLES."\"><pre>$context<br>" . $exported . "</pre></div>";
        }
    }
}

if (!function_exists('stackTrace')) {
    /**
     * Outputs a stack trace based on the supplied options.
     *
     * ### Options
     *
     * - `depth` - The number of stack frames to return. Defaults to 50
     * - `start` - The stack frame to start generating a trace from. Defaults to 1
     *
     * @param array $options Format for outputting stack trace
     * @return mixed Formatted stack trace
     * @link https://github.com/cakephp/cakephp/blob/master/src/basics.php
     */
    function stackTrace(array $options = array())
    {
        if (!WP_DEBUG) {
            return;
        }

        $options += array('start' => 0);
        $options['start']++;

        if (Strata::isCommandLineInterface() || Strata::isBundledServer()) {
            $options['output'] = Debugger::CONSOLE;
            Strata::getLogger("StrataConsole")->debug(Debugger::trace($options));
        }

        $options['output'] = Debugger::HTML;
        echo Debugger::trace($options);
    }
}

if (!function_exists('breakpoint')) {
    /**
     * Command to return the eval-able code to startup PsySH in interactive debugger
     * Works the same way as eval(\Psy\sh());
     * psy/psysh must be loaded in your project
     * @link http://psysh.org/
     * @link https://github.com/cakephp/cakephp/blob/master/src/basics.php
     * @return string
     */
    function breakpoint()
    {
        if (!WP_DEBUG) {
            return;
        }

        if ((PHP_SAPI === 'cli-server' || PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') && class_exists('\Psy\Shell')) {
            return 'extract(\Psy\Shell::debug(get_defined_vars(), isset($this) ? $this : null));';
        }

        trigger_error(
            "psy/psysh must be installed and you must be in a CLI environment to use the breakpoint function",
            E_USER_WARNING
        );
    }
}
