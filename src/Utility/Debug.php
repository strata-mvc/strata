<?php
if (!function_exists('debug')) {

    /**
     * Prints out better looking debug information about a variable.
     * This echo directly where it is called.
     * @ignore
     */
    function debug($mixed)
    {
        $header =  "=======[Debug]=======";
        $debug =  "(".gettype($mixed) . ") " . var_export($mixed, true);
        $footer =  "=======[Debug]=======";

        $app = \Strata\Strata::app();
        $app->log("", "[Strata::debug]");
        $app->log($debug, "[Strata::debug]");
        $app->log("", "[Strata::debug]");


        echo "<pre>".$header."\n";
        echo $debug;
        echo "\n\n<div style=\"overflow:auto; font-size: 12px; font-family: consolas; background:transparent; width:100%; height:80px;\">";
        debug_print_backtrace();
        echo "</div>";
        echo "\n".$footer."</pre>";
    }
}
