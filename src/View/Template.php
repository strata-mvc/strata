<?php
namespace MVC\View;

class Template {

    /**
     * @param string The name of the template to load (.php will be added to it)
     * @param array an associative array of values to assign in the template
     */
    public static function render($name, $values = array())
    {
        ob_start();
        // expose local variables for the template
        extract($values);
        include(get_template_directory() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $name . '.php');
        return  ob_get_clean();
    }
}
