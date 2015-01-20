<?php

namespace MVC;

class Controller {

    public $viewVars = array();

    // These hooks allow views to use wordpress nicely, but still trigger
    // items in the current controller
    public $shortcodes = array();

    public $router = null;
    public $app = null;

    public function init()
    {
        // Expose a reference to the current controller
        $GLOBALS['Controller'] = $this;

        // If this controller has shortcodes, try to assign them.
        $this->_buildShortcodes();

    }

    public function after()
    {

    }

    public function before()
    {

    }

    public function index()
    {

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
            "Content-disposition" => null,
            "content" => "",
            "end" => true
        );

        if (is_array($options['content'])) {
            $content = json_encode($options['content']);
        } else {
            $content = $options['content'];
        }

        // When we have to end the process upon rendering, expected behaviour
        // is an ajax request. Set the header as we will not use wordpress'.
        if ($options['end']) {
            header('Content-type: ' . $options['Content-type']);

            if (!is_null($options['Content-disposition'])) {
                header('Content-disposition: ' . $options['Content-disposition']);
            }

            echo $content;
            exit();
        }

        echo $content;
    }

    /**
     * Register dynamic shortcodes hooks to the instanciated controller
     */
    protected function _buildShortcodes()
    {
        if (count($this->shortcodes) > 0) {
            foreach ($this->shortcodes as $shortcode => $methodName) {
                if(method_exists($this, $methodName)) {
                    add_shortcode($shortcode, array($this, $methodName));
                }
            }
        }
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
