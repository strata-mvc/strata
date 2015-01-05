<?php

namespace MVC;

class Controller {

    public $viewVars = array();

    // These hook allow views to use wordpress nicely, but still trigger
    // items in the current controller
    public $shortcodes = array();

    public $router = null;
    public $app = null;

    public function init()
    {
        $GLOBALS['Controller'] = $this;
    }

    public function after()
    {

    }

    public function before()
    {

    }

    public function index()
    {
        /**
         * This function also catches AJAX calls inside the backend because of how WP works.
         * Ensure the process is not broken if the request is not intended to us.
         */
        if (defined('DOING_AJAX') && method_exists($this, $_POST['action'])) {
            call_user_func_array(array($this, $_POST['action']), func_get_args());
        }
    }

    /**
     * Assigns the variable to the current view
     */
    public function set($name, $value)
    {
        $this->viewVars[$name] = $value;
        // Not super pretty, but this is the only way I could think of reaching WP's scope.
        $GLOBALS[$name] = $value;
    }

    public function posted()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
    }

    public function makeSecure()
    {
        check_ajax_referer( SECURITY_SALT, 'security' );
    }

    /**
     * Renders on the page and end the process. Usefull for simple HTML returns or Ajax requests.
     */
    public function render($options)
    {
        $options += array(
            "Content-type" => "text/html",
            "content" => ""
        );

        header('Content-type: ' . $options['Content-type']);

        if (is_array($options['content'])) {
            $content = json_encode($options['content']);
        } else {
            $content = $options['content'];
        }

        echo $content;
        die();
    }

    /**
     * @param string The name of the template to load (.php will be added to it)
     * @param array an associative array of values to assign in the template
     */
    public static function loadTemplate($name, $values = array())
    {
        ob_start();
        // expose local variables for the template
        extract($values);
        include(get_template_directory() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $name . '.php');
        return  ob_get_clean();
    }

}
