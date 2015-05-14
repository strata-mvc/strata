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

        error_log("\n\n\e[0;36m".$header . "\e[0m\n" . $debug . "\n\e[0;36m" . $footer . "\e[0m\n");

        echo "<pre>".$header."\n";
        echo $debug;
        echo "\n\n<div style=\"overflow:auto; font-size: 12px; font-family: consolas; background:transparent; width:100%; height:80px;\">";
        debug_print_backtrace();
        echo "</div>";
        echo "\n".$footer."</pre>";
    }
}
