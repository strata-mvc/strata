<?php

namespace Strata\Controller;

use Strata\Controller\Request;
use Strata\View\Template;

/**
 * Base controller class.
 */
class Controller {

    /**
     * The current request
     *
     * @var Strata\Controller\Request
     */
    public $request = null;

    /**
     * The associated view template
     *
     * @var Strata\View\Template
     */
    public $template = null;


    /**
     * These hooks allow views to use Wordpress nicely, but still trigger
     * items in the current controller.
     *
     * @var  array
     */
    public $shortcodes = array();

    /**
     * Initiate the controller.
     * @return null
     */
    public function init()
    {
        // Save the current request
        $this->request = new Request();
        $this->template = new Template();

        // If this controller has shortcodes, try to assign them.
        $this->_buildShortcodes();
    }

    /**
     * Executed after each calls to a controller action.
     * @return null
     */
    public function after()
    {

    }

    /**
     * Executed before each calls to a controller action.
     * @return null
     */
    public function before()
    {

    }

    /**
     * Base action. This is used mainly as a precautionary fallback.
     * @return  null
     */
    public function index()
    {

    }

    /**
     * Assigns the variable to the current view
     */
    public function set($name, $value)
    {
        $this->viewVars[$name] = $value;
        // Not super pretty, but this is the only way I could think
        // of for reaching WP's scope.
        $GLOBALS[$name] = $value;
    }

    /**
     * Renders on the page and end the process. Useful for simple HTML returns or data in another format like JSON.
     * @param  array $options An associative array of rendering options
     * @return string          The rendered content.
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
     * Registers dynamic shortcodes hooks to the instantiated controller.
     * Note that these are not available when this instance of the controller
     * is not being loaded.
     * @return  null
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
}
