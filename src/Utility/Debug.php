<?php
if (!function_exists('debug')) {

    /**
     * Prints out better looking debug information about a variable.
     * This echo directly where it is called.
     * @ignore
     */
    function debug($mixed)
    {
        echo "<pre>=======[Debug]=======\n";
        echo "(".gettype($mixed) . ") " ;
        var_export($mixed);
        echo "\n\n<div style=\"overflow:auto; font-size: 12px; font-family: consolas; background:transparent; width:100%; height:80px;\">";
        debug_print_backtrace();
        echo "</textarea>";
        echo "\n=======[Debug]=======</pre>";
    }
}
