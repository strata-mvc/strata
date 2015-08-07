<?php
namespace Strata\View;

use Strata\Strata;

class Template {

    /**
     * Parses a template file and declares view variables in this scope for the
     * template to have access to them.
     * @param string The name of the template to load
     * @param array an associative array of values to assign in the template
     * @param string The file extension of the template to be loaded
     * @return  string The parsed html.
     */
    public static function parse($name, $variables = array(), $extension = '.php')
    {
        $templateFilePath = implode(DIRECTORY_SEPARATOR, array(get_template_directory(), 'templates', $name . $extension));
        return Template::parseFile($templateFilePath, $variables);
    }

    public static function parseFile($templateFilePath, $variables = array())
    {
        ob_start();

        // Print debug info in the the logs
        if (Strata::isDev()) {
            $partialFilePath = str_replace(ABSPATH, "", $templateFilePath);
            $vars = array_keys($variables);
            $debugComment = sprintf("Template '%s' with variables: %s", $templateFilePath, implode(", ", $vars));
            $app = Strata::app();
            $app->log($debugComment, "[Strata::Template]");

            // also print in the html as a comment
            echo "<!-- " . $debugComment . " -->";
        }


        extract($variables);
        include($templateFilePath);
        return ob_get_clean();
    }
}
