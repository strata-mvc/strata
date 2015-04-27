<?php
/**
 */
namespace Strata\Shell;

/**
 * built-in Server Shell
 */
class DocumentationShell extends \Strata\Shell\Shell
{

    public function initialize($options = array())
    {
        $this->_config = $options + array(
            "destination"  => implode(DIRECTORY_SEPARATOR, array(\Strata\Strata::getRootPath(), "doc", DIRECTORY_SEPARATOR)),
            "theme"  => "theme-bootstrap",
        );

        parent::initialize($options);
    }

    public function contextualize($args)
    {
        if (count($args) > 3) {
            $this->_config["destination"] = $args[3];
        }

        if (count($args) > 4) {
            $this->_config["theme"] = $args[4];
        }

        parent::contextualize($args);
    }

    /**
     * Override main() to handle action
     *
     * @return void
     */
    public function main()
    {
        $this->startup();

        $this->_deletePrevious();

        $this->_generateAPI();
        $this->nl();

        $this->_generateThemesApi();
        $this->nl();

        $this->_summary();
        $this->nl();

        $this->shutdown();
    }

    protected function _summary()
    {
        $this->out("The project documentation has been generated at the following URLs: ");
        $this->nl();

        $this->out($this->info("API               : ") . $this->_config["destination"] . 'api/index.html');
        $this->out($this->info("Theme Information : ") . $this->_config["destination"] . 'wpdoc/index.html');
    }

    protected function _deletePrevious()
    {
        $this->_rrmdir($this->_config["destination"] . 'api');
        $this->_rrmdir($this->_config["destination"] . 'wpdoc');
    }

    protected function _generateAPI()
    {
        $srcPath = \Strata\Strata::getSRCPath();
        $destination = $this->_config["destination"] . 'api';
        $apigen = implode(DIRECTORY_SEPARATOR, array(\Strata\Strata::getOurVendorPath() . "vendor", "apigen", "apigen", "bin", "apigen"));

        $this->out($this->info("Generating API"));
        $this->out($this->tree(true) . "Scanning $srcPath");
        $this->nl();

        system(sprintf("%s generate -s %s -d %s --quiet", $apigen, $srcPath, $destination));
    }

    protected function _generateThemesApi()
    {
        $srcPath = \Strata\Strata::getSRCPath();

        $this->out($this->info("Generating Wordpress theme details"));

        $info = $this->_scanThemeDirectories(\Strata\Strata::getThemesPath());
        $this->_writeThemesDocumentation($info);
    }

    /**
     * @todo  This will do for now, but we may need to steal the styles from apigen's theme to make sure things look the same.
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    protected function _writeThemesDocumentation($info)
    {
        $header = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Overview</title>';
        $header .= '<link rel="stylesheet" href="../api/resources/style.css">';
        $header .= '</head><body><div id="content">';
        $content = '<h1>Project snapshot</h1>';
        $footer = '</div></body></html>';
        $htmlTpl = "<tr><td class=\"attributes\"><strong>%s</strong></td><td class=\"name\"><div><code>%s</code><div class=\"description short\">%s</div></div></td></tr>";

        foreach ($info["themes"] as $themeName => $theme) {
            $content .= "<h2>$themeName</h2>";
            $content .= '<table class="summary" id="templates" style="width:45%; float:left;">';
            $content .= "<caption>This theme defines " . count($theme["templates"]) . " template files.</caption><tbody>";

            foreach ($theme["templates"] as $template) {
                $content .= sprintf($htmlTpl,
                        empty($template["Template Name"]) ? 'Name not specified' : $template["Template Name"],
                        $template["filename"],
                        empty($template["Description"]) ? 'Missing description.' : $template["Description"]
                );
            }
            $content .= "</tbody></table>";

            $content .= '<table class="summary" id="libs" style="width:45%; margin-left:2%; float:left;">';
            $content .= "<caption>This theme uses " . count($theme["libs"]) . " obvious library files.</caption><tbody>";
            foreach ($theme["libs"] as $lib) {
                $content .= sprintf($htmlTpl,
                        empty($lib["Name"]) ? 'Name not specified' : $lib["Name"],
                        $lib["filename"],
                        empty($lib["Description"]) ? 'Missing description.' : $lib["Description"]
                );
            }
            $content .= "</tbody></table>";
        }

        if (!is_dir($this->_config["destination"] . 'wpdoc')) {
            mkdir($this->_config["destination"] . 'wpdoc');
        }

        file_put_contents($this->_config["destination"] . 'wpdoc/index.html', $header . $content . $footer, LOCK_EX);
    }

    protected function _scanThemeDirectories($base)
    {
        $tree = array("themes" => array());

        $di = new \RecursiveDirectoryIterator($base);
        foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {
            // Obtain the theme scope, add it to the stack if it wasn't
            // saved yet.
            $theme = null;
            if (preg_match("/themes\/(.+?)\//", $filename, $matches)) {
                $theme = $matches[1];
                if (!array_key_exists($theme, $tree["themes"])) {
                    $tree["themes"][$theme] = array(
                        "templates" => array(),
                        "libs"      => array()
                    );

                    $this->out($this->tree(true) . "Scanning $theme");
                }
            }

            // Match for wordpress templates
            if (preg_match("/themes\/$theme\/template\-(.+?)\.php/", $filename, $matches)) {
                $template = $matches[1];
                if (!array_key_exists($template, $tree["themes"][$theme]["templates"])) {
                    $tree["themes"][$theme]["templates"][$template] = array();

                    $headerKeys = array("Template Name" => "Template Name", "Description" => "Description");
                    $templateDetails = $this->_getFileData($filename, $headerKeys);

                    $tree["themes"][$theme]["templates"][$template]["filename"] = $filename;
                    foreach ($headerKeys as $key) {
                        $tree["themes"][$theme]["templates"][$template][$key] = $templateDetails[$key];
                    }

                   // $this->out($this->tree() . $template);
                }
            }

            // Match for custom lib files
            if (preg_match("/themes\/$theme\/lib\/(.+?)\.php/", $filename, $matches)) {
                $lib = $matches[1];

                if (!array_key_exists($lib, $tree["themes"][$theme]["libs"])) {
                    $tree["themes"][$theme]["libs"][$lib] = array();

                    $headerKeys = array("Name" => "Name", "Description" => "Description");
                    $libDetails = $this->_getFileData($filename, $headerKeys);

                    $tree["themes"][$theme]["libs"][$lib]["filename"] = $filename;
                    foreach ($headerKeys as $key) {
                        $tree["themes"][$theme]["libs"][$lib][$key] = $libDetails[$key];
                    }
                    //$this->out($this->tree() . $lib);
                }
            }
        }

        return $tree;
    }

    /**
     * This is a lightly modified copy of Wordpress get_file_data() found in wp-includes/functions.php.
     * Because we don't load Wordpress when executing Strata Shell, we don't have access to it.
     * @todo  Allow a way for Strata to load Wordpress on demand.
     * @param  [type] $file            [description]
     * @param  [type] $default_headers [description]
     * @param  string $context         [description]
     * @return [type]                  [description]
     */
    protected function _getFileData($file, $all_headers = array(), $context = '')
    {
        // We don't need to write to the file, so just open for reading.
        $fp = fopen( $file, 'r' );

        // Pull only the first 8kiB of the file in.
        $file_data = fread( $fp, 8192 );

        // PHP will close file handle, but we are good citizens.
        fclose( $fp );

        // Make sure we catch CR-only line endings.
        $file_data = str_replace( "\r", "\n", $file_data );

        /**
         * Filter extra file headers by context.
         *
         * The dynamic portion of the hook name, `$context`, refers to
         * the context where extra headers might be loaded.
         *
         * @since 2.9.0
         *
         * @param array $extra_context_headers Empty array by default.
         */

        foreach ( $all_headers as $field => $regex ) {
            if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] )
                $all_headers[ $field ] = $match[1];
            else
                $all_headers[ $field ] = '';
        }

        return $all_headers;

    }

    protected function _rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") $this->_rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
