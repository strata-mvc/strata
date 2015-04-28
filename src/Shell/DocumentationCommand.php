<?php
/**
 */
namespace Strata\Shell;

use Strata\Shell\StrataCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * built-in Server Shell
 */
class DocumentationCommand extends StrataCommand
{
    protected function configure()
    {
        $this
            ->setName('document')
            ->setDescription('Documents the current app')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('destination', 'c', InputOption::VALUE_OPTIONAL),
                ))
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        $this->_deletePrevious();

        $this->_generateAPI();
        $this->nl();

        $this->_generateThemesApi();
        $this->nl();

        $this->_summary();
        $this->nl();

        $this->shutdown();
    }

    protected function _getDestination()
    {
        if (!is_null($this->_input->getOption('destination'))) {
            return $this->_input->getOption('destination');
        }

        return implode(DIRECTORY_SEPARATOR, array(\Strata\Strata::getRootPath(), "doc", DIRECTORY_SEPARATOR));
    }

    protected function _getApiDestination()
    {
        return $this->_getDestination() . 'api';
    }
    protected function _getWpdocDestination()
    {
        return $this->_getDestination() . 'wpdoc';
    }

    protected function _getApigenBin()
    {
        return implode(DIRECTORY_SEPARATOR, array(\Strata\Strata::getOurVendorPath() . "vendor", "apigen", "apigen", "bin", "apigen"));;
    }

    protected function _summary()
    {
        $this->_output->writeLn("The project documentation has been generated at the following URLs: ");
        $this->nl();

        $destination = $this->_getDestination();
        $this->_output->writeLn("<info>API               :</info> ". $this->_getApiDestination()   ."/index.html");
        $this->_output->writeLn("<info>Theme Information :</info> ". $this->_getWpdocDestination() ."/index.html");
    }

    protected function _deletePrevious()
    {
        $this->_rrmdir($this->_getApiDestination());
        $this->_rrmdir($this->_getWpdocDestination());
    }

    protected function _generateAPI()
    {
        $srcPath = \Strata\Strata::getSRCPath();

        $this->_output->writeLn("<info>Generating API</info>");
        $this->_output->writeLn($this->tree(true) . "Scanning $srcPath");
        $this->nl();

        system(sprintf("%s generate -s %s -d %s --quiet", $this->_getApigenBin(), $srcPath, $this->_getApiDestination()));
    }

    protected function _generateThemesApi()
    {
        $this->_output->writeLn("<info>Generating Wordpress theme details</info>");

        $themesPath = \Strata\Strata::getThemesPath();
        $info = $this->_scanThemeDirectories($themesPath);
        $this->_writeThemesDocumentation($info);
    }

    /**
     * @todo  This will do for now, but the template shouldn't be hardcoded.
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

        $wpdocdir = $this->_getWpdocDestination();
        if (!is_dir($wpdocdir)) {
            mkdir($wpdocdir);
        }

        file_put_contents($wpdocdir . "/index.html", $header . $content . $footer, LOCK_EX);
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

                    $this->_output->writeLn($this->tree(true) . "Scanning $theme");
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

                   // $this->_output->writeLn($this->tree() . $template);
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
                    //$this->_output->writeLn($this->tree() . $lib);
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
