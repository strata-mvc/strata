<?php
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

        $header =  "=======[Debug]=======";
        $debug =  "(".gettype($mixed) . ") ";
        ob_start();
        var_dump($mixed);
        $debug .= ob_get_clean();

        $footer =  "=======[Debug]=======";

        $context = "unknown context at unknown line";
        foreach (debug_backtrace() as $idx => $file) {
            if ($file['file'] != __FILE__) {
                $last = explode(DIRECTORY_SEPARATOR, $file['file']);
                $context = sprintf("In %s at line %s: ", $last[count($last)-1], $file['line']);
                break;
            }
        }

        $app = \Strata\Strata::app();
        $app->log($context, "[Strata::debug]");
        $app->log($debug, "[Strata::debug]");
        $app->log($context, "[Strata::debug]");


        if (\Strata\Strata::isCommandLineInterface()) {
            echo $header."\n".$debug ."\n".$footer."\n";
        } else {
            echo "<pre>".$header."\n";
            echo $debug;
            echo "\n\n<div style=\"overflow:auto; font-size: 12px; font-family: consolas; background:transparent; width:100%; height:80px;\">";
            debug_print_backtrace();
            echo "</pre>";
            echo "\n".$footer."</pre>";
        }
    }
}

// This is on it way but we need to fix the server shell beforehand.
// if (!function_exists('breakpoint')) {
//     /**
//      * Command to return the eval-able code to startup PsySH in interactive debugger
//      * Works the same way as eval(\Psy\sh());
//      * psy/psysh must be loaded in your project
//      * @link http://psysh.org/
//      * @link https://github.com/cakephp/cakephp/blob/master/src/basics.php
//      * @return string
//      */
//     function breakpoint()
//     {
//         if ((PHP_SAPI === 'cli-server' || PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') && class_exists('\Psy\Shell')) {
//              return eval(extract(\Psy\Shell::debug(get_defined_vars(), isset($this) ? $this : null)));
//         }

//         trigger_error(
//             "psy/psysh must be installed and you must be in a CLI environment to use the breakpoint function",
//             E_USER_WARNING
//         );
//     }
// }
