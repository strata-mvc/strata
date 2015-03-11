<?php

/**
 * Offer a prettier way of formating debugging output on screen
 */
if (!function_exists('debug')) {
    function debug($var)
    {
        echo "<pre>=======[Debug]=======\n";
        echo "(".gettype($var) . ") " ;
        var_export($var);
        echo "\n\n<div style=\"overflow:auto; font-size: 12px; font-family: consolas; background:transparent; width:100%; height:80px;\">";
        debug_print_backtrace();
        echo "</textarea>";
        echo "\n=======[Debug]=======</pre>";
    }
}
