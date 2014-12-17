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
        echo "\n\n<textarea style=\"background:transparent; width:100%; height:80px;\">";
        debug_print_backtrace();
        echo "</textarea>";
        echo "\n=======[Debug]=======</pre>";
    }
}
