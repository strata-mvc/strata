<?php

namespace Strata\View;

use Strata\Strata;
use Strata\Core\StrataConfigurableTrait;

/**
 * The template object is used to generate views.
 */
class Template
{
    use StrataConfigurableTrait;

    /**
     * @var string A unique code to identify where the template
     * should yield the generated contents.
     */
    const TPL_YIELD = "__STRATA_YIELD__";

    /**
     * Parses a template file and declares view variables in this scope for the
     * template to have access to them. Loads localized templates based on the current
     * active locale.
     * @param string The name of the template to load
     * @param array an associative array of values to assign in the template
     * @param string The file extension of the template to be loaded
     * @return  string The parsed html.
     */
    public static function parse($path, $variables = array(), $extension = '.php', $allowDebug = true)
    {
        $tpl = new self();

        $tpl->injectVariables($variables);
        $tpl->setViewName($path);
        $tpl->setConfig("file_extention", $extension);

        if (!(bool)$tpl->getConfig("allow_debug")) {
            $tpl->setConfig("allow_debug", false);
        } else {
            $tpl->setConfig("allow_debug", $allowDebug);
        }

        return $tpl->compile();
    }

    /**
     * Loads up the file located at $templateFilePath, assigns it $variables
     * and saves the generated HTML.
     * @param  string  $templateFilePath
     * @param  array   $variables
     * @param  boolean $allowDebug
     * @return string
     */
    public static function parseFile($templateFilePath, $variables = array(), $allowDebug = true)
    {
        ob_start();

        // Print debug info in the the logs
        // only when Strata is running as the development
        // environment.
        if (Strata::isDev()) {
            $app = Strata::app();
            $startedAt = microtime(true);

            // Print in the html as a comment
            $partialFilePath = defined('ABSPATH') ?
                str_replace(dirname(dirname(ABSPATH)), "", $templateFilePath) :
                $templateFilePath;

            if ($allowDebug) {
                echo "\n<!-- [Strata::Template:Begin] -->\n<!--\n     Source    : .$partialFilePath \n     Variables : ".implode(", ", array_keys($variables))."\n -->\n";
            }
        }

        extract($variables);
        include $templateFilePath;

        if (Strata::isDev()) {
            if ($allowDebug) {
                echo "\n<!-- [Strata::Template:End] -->";
            }

            $executionTime = microtime(true) - $startedAt;
            $timer = sprintf(" (Done in %s seconds)", round($executionTime, 4));
            $app->log($partialFilePath . $timer, "[Strata:Template]");
        }

        return ob_get_clean();
    }

    /**
     * @var array Variables available only in the scope of the template
     */
    private $contextualVariables = array();

    /**
     * @var string The name o the
     */
    private $viewName = "index";

    public function __construct()
    {
        $this->configure(array(
            "view_source_path" => get_template_directory() . DIRECTORY_SEPARATOR . 'templates',
            "use_localized_views" => true,
            "file_extention" => ".php",
            "allow_debug" => true,
        ));
    }

    /**
     * Injects variables to the template scope.
     * @param  array  $vars
     */
    public function injectVariables(array $vars)
    {
        $this->contextualVariables = $vars;
    }

    /**
     * Sets the view name which will define the name of file used
     * to build the view.
     * @param string $name
     */
    public function setViewName($name)
    {
        $this->viewName = $name;
    }

    /**
     * Compiles the generated contents of the current template
     * configuration.
     * @return string
     */
    public function compile()
    {
        $templateFilePrefix = $this->hasLocalizedVersion() ?
            $this->generateLocalizedViewPath() :
            $this->generateDefaultViewPath();

        $content = self::parseFile(
            $templateFilePrefix . $this->getConfig('file_extention'),
            $this->contextualVariables,
            (bool)$this->getConfig('allow_debug')
        );

        if (is_null($this->getConfig("layout"))) {
            return $content;
        }

        $layout = self::parseFile(
            $this->generateDefaultLayoutPath() . $this->getConfig('file_extention'),
            $this->contextualVariables,
            (bool)$this->getConfig('allow_debug')
        );

        return str_replace(self::TPL_YIELD, $content, $layout);
    }

    /**
     * Checks whether there is a localized version of the current view in the
     * current Locale.
     * @return boolean
     */
    protected function hasLocalizedVersion()
    {
        $app = Strata::app();
        if ((bool)$this->getConfig("use_localized_views") && $app->i18n->hasActiveLocales()) {
            $localizedFilename =  $this->generateLocalizedViewPath();
            return file_exists($localizedFilename . $this->getConfig('file_extention'));
        }

        return false;
    }

    /**
     * Generates the path of where the view localized in the current locale would
     * be located.
     * @return string
     */
    protected function generateLocalizedViewPath()
    {
        $app = Strata::app();
        return $this->generateDefaultViewPath() . "." . $app->i18n->getCurrentLocaleCode();
    }

    /**
     * Generates the path of where the basic view would be located.
     * @return string
     */
    protected function generateDefaultViewPath()
    {
        return $this->generateDefaultViewLocation() . $this->viewName;
    }

    /**
     * Generates the path of where the basic layout file would be located.
     * @return string
     */
    protected function generateDefaultLayoutPath()
    {
        return $this->generateDefaultViewLocation() . $this->getConfig("layout");
    }

    /**
     * Generates the path of where the view files are located.
     * @return string
     */
    protected function generateDefaultViewLocation()
    {
        return $this->getConfig("view_source_path") . DIRECTORY_SEPARATOR;
    }
}
