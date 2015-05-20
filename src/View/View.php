<?php
namespace Strata\View;

use Strata\View\Template;

/**
 * Handles the generation of view html. It is important to understand this is not used
 * when a request continues the regular Wordpress flow which generates its own templates.
 *
 * This class is used when a controller is printing the view object itself, as it is often the
 * case for AJAX, file downloads and numeric returns.
 */
class View {

    /**
     * Rendering options, used when explicitly rendering a view from a controller.
     * @var array
     */
    protected $_options = array();

    /**
     * The list of variables to be declared when loading the view
     * @var array
     */
    protected $_templateVars = array();

    /**
     * The constructor of the View class populates the default options.
     */
    function __construct()
    {

    }

    public function loadTemplate($path)
    {
        return Template::parse($path, $this->getVariables());
    }

    /**
     * Assigns the variable to the current view
     */
    public function set($name, $value)
    {
        $this->_templateVars[$name] = $value;

        // Not super pretty, but this is the only way I could think
        // of for reaching WP's scope.
        $GLOBALS[$name] = $value;
    }

    /**
     * Returns the list of currently declared variables in this view.
     * @return array An associative array of values
     */
    public function getVariables()
    {
        return $this->_templateVars;
    }

    /**
     * Renders on the page and end the process. Useful for simple HTML returns or data in another format like JSON.
     * @param  array $options An associative array of rendering options
     * @return string          The rendered content.
     */
    public function render($options = array())
    {
        $this->_options = $options + array(
            "Content-type" => "text/html",
            "Content-disposition" => null,
            "content" => "",
            "end" => false
        );

        $content = $this->_parseCurrentContent();

        if ((bool)$this->_options['end']) {
            // Only play with headers when we know we will
            // kill the process.
            $this->_applyHeaders();

            echo $content;
            exit();
        }

        echo $content;
    }

    /**
     * Takes the current value of the content option and ensure that it is
     * correctly formatted for output. If the content is an array or an object, the value.
     * is encoded to a json string. Otherwise, the content simply cast into string.
     * @return string The content of the view
     */
    protected function _parseCurrentContent()
    {
        if (is_array($this->_options['content']) || is_object($this->_options['content'])) {
            return json_encode($this->_options['content']);
        }

        return "" . $this->_options['content'];
    }

    /**
     * Applies header values sent as options. For the time being, only Content-type and Content-disposition
     * are supported.
     * @return null
     */
    protected function _applyHeaders()
    {
        if ($this->_options['Content-type'] != "text/html") {
            header('Content-type: ' . $this->_options['Content-type']);
        }

        if (!is_null($this->_options['Content-disposition'])) {
            header('Content-disposition: ' . $this->_options['Content-disposition']);
        }
    }

}
