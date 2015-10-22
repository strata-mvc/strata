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
        $debug =  "(".gettype($mixed) . ") " . var_export($mixed, true);
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
            echo "</div>";
            echo "\n".$footer."</pre>";
        }
    }
}
