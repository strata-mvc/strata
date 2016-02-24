<?php

namespace Strata\View;

use Strata\Strata;
use Strata\Core\StrataConfigurableTrait;

class Template
{
    use StrataConfigurableTrait;

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

    public static function parseFile($templateFilePath, $variables = array(), $allowDebug = true)
    {
        ob_start();

        // Print debug info in the the logs
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

    private $contextualVariables = array();
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

    public function injectVariables(array $vars)
    {
        $this->contextualVariables = $vars;
    }

    public function setViewName($name)
    {
        $this->viewName = $name;
    }

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

    protected function hasLocalizedVersion()
    {
        $app = Strata::app();
        if ((bool)$this->getConfig("use_localized_views") && $app->i18n->hasActiveLocales()) {
            $localizedFilename =  $this->generateLocalizedViewPath();
            return file_exists($localizedFilename . $this->getConfig('file_extention'));
        }

        return false;
    }

    protected function generateLocalizedViewPath()
    {
        $app = Strata::app();
        return $this->generateDefaultViewPath() . "." . $app->i18n->getCurrentLocaleCode();
    }

    protected function generateDefaultViewPath()
    {
        return $this->generateDefaultViewLocation() . $this->viewName;
    }

    protected function generateDefaultLayoutPath()
    {
        return $this->generateDefaultViewLocation() . $this->getConfig("layout");
    }

    protected function generateDefaultViewLocation()
    {
        return $this->getConfig("view_source_path") . DIRECTORY_SEPARATOR;
    }
}
