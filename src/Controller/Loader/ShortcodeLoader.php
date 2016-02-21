<?php

namespace Strata\Controller\Loader;

use Strata\Utility\Hash;
use Strata\Utility\Inflector;
use Strata\Controller\Controller;
use Exception;

/**
 * Automated the declaration of Wordpress Shortcodes within a Strata Controller object.
 * @see https://codex.wordpress.org/Shortcode_API
 * @see http://strata.francoisfaubert.com/docs/controllers/
 */
class ShortcodeLoader
{
    /**
     * The Strata Controller instance towards which the shortcodes callbacks
     * will route.
     * @var Controller;
     */
    private $controller = null;

    /**
     * A list of normalized shortcodes and shortcode configuration.
     * Shortcode names should be all lowercase and use all letters, numbers and underscores.
     * @var array
     */
    private $shortcodes = array();

    /**
     * Shortcode loader constructor builds a list of shortcode configurations
     * associated to a controller and instantiates them as dynamic callbacks.
     * @param Controller $controller
     */
    public function __construct(Controller $controller)
    {
        if (is_null($controller)) {
            throw new Exception("No controller has been defined for shortcode callback.");
        }

        $this->controller = $controller;
        $this->shortcodes = $this->getNormalizedShortcodes();
    }

    /**
     * Specifies if a number of shortcodes have been defined.
     * @return boolean True if some are defined.
     */
    public function hasShortcodes()
    {
        return count($this->shortcodes) > 0;
    }

    /**
     * Registers dynamic shortcode hooks to the instantiated controller.
     * Note that these are not available when this instance of the controller
     * is not being loaded.
     * @return  null
     */
    public function register()
    {
        if ($this->hasShortcodes()) {
            foreach ($this->shortcodes as $shortcode => $methodName) {
                if (method_exists($this->controller, $methodName)) {
                    add_shortcode($this->formatCode($shortcode), array($this->controller, $methodName));
                }
            }
        }
    }

    /**
     * Unregisters the list of shortcodes in Wordpress.
     * @return null
     */
    public function unregister()
    {
        if ($this->hasShortcodes()) {
            foreach ($this->shortcodes as $shortcode => $methodName) {
                remove_shortcode($this->formatCode($shortcode));
            }
        }
    }

    /**
     * Returns a normalized list of controller shortcodes.
     * @return array
     */
    private function getNormalizedShortcodes()
    {
        if (isset($this->controller->shortcodes)) {
            return (array)$this->controller->shortcodes;
        }

        return array();
    }

    /**
     * Per Wordpress' configuration, shortcodes must be
     * formatted roughly as underscored function names.
     * @param  string $shortcode
     * @return string Shortcode with enforced formatting
     */
    private function formatCode($shortcode)
    {
        return Inflector::underscore($shortcode);
    }
}
