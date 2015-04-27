<?php
/**
 */
namespace Strata\Shell;

use Strata\Shell\Shell;
use Exception;

/**
 * Strata self maintaining Shell
 */
class StrataShell extends Shell
{
    protected $_seemsFine = true;

    protected $_type = null;

    protected $_directoryStructure = array(
        "bin",
        "bin/vagrant",
        "db",
        "doc",
        "log",
        "src",
        "src/Controller",
        "src/Model",
        "src/View",
        "src/View/helper",
        "test",
        "test/Controller",
        "test/Model",
        "test/View",
        "test/View/Helper",
        "tmp"
    );

    protected $_srcUrl = "https://raw.githubusercontent.com/francoisfaubert/strata-template-files/master/src/";

    protected $_starterFiles = array(
        'AppController.php' => 'src/controller/AppController.php',
        'AppModel.php'      => 'src/model/AppModel.php',
        'AppHelper.php'     => 'src/view/helper/AppHelper.php',
        'strata-bootstraper.php' => 'web/app/mu-plugins/strata-bootstraper.php',
    );

    public function contextualize($args)
    {
        if (count($args) > 2) {
            switch($args[3]) {
                case "install"      : $this->_type = "install"; break;
                case "uninstall"    : $this->_type = "uninstall"; break;
                default             : throw new Exception("That is not a valid command.");
            }
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

        switch ($this->_type) {
            case "install"   : $this->_install(); break;
            case "uninstall" : $this->_uninstall(); break;
        }

        $this->shutdown();
    }

    protected function _install()
    {
        $this->_createDirectoryStructure();
        $this->_createStarterFiles();
        $this->_installDone();
    }

    protected function _uninstall()
    {
        $this->_removeStarterFiles();
        $this->_removeDirectoryStructure();
        $this->_uninstallDone();
    }

    protected function _createDirectoryStructure()
    {
        $this->out("Ensuring correct directory structure.");

        $count = 0;
        foreach ($this->_directoryStructure as $dir) {
            $label = $this->tree(++$count >= count($this->_directoryStructure));
            if (!is_dir($dir)) {
                if (mkdir($dir)) {
                    $this->out($label . $this->ok($dir));
                } else {
                    $this->out($label . $this->fail($dir));
                    $this->_flagFailing();
                }
            } else {
                $this->out($label . $this->skip($dir));
            }
        }

        $this->nl();
    }

    protected function _removeDirectoryStructure()
    {
        $this->out("Attempting to remove directory structure.");

        $count = 0;
        foreach (array_reverse($this->_directoryStructure) as $dir) {
            $label = $this->tree(++$count >= count($this->_directoryStructure));
            if (is_dir($dir)) {
                if (@rmdir($dir)) {
                    $this->out($label . $this->ok($dir));
                } else {
                    $this->out($label . $this->fail($dir). $this->error(" Is it empty?"));
                    $this->_flagFailing();
                }
            } else {
                $this->out($label . $this->skip($dir));
            }
        }

        $this->nl();
    }

    protected function _createStarterFiles()
    {
        $this->out("Ensuring project files are present.");

        $count = 0;
        foreach ($this->_starterFiles as $source => $file) {
            $label = $this->tree(++$count >= count($this->_starterFiles));
            if (!file_exists($file)) {
                if (file_put_contents($file, fopen($this->_srcUrl . $source, 'r')) > 0) {
                    $this->out($label . $this->ok($file));
                } else {
                    $this->out($label . $this->fail($file));
                    $this->_flagFailing();
                }
            } else {
                $this->out($label . $this->skip($file));
            }
        }

        $this->nl();
    }

    protected function _removeStarterFiles()
    {
        $this->out("Attempting to remove starter files.");

        $count = 0;
        foreach ($this->_starterFiles as $file) {
            $label = $this->tree(++$count >= count($this->_starterFiles));
            if (file_exists($file)) {
                if (unlink($file)) {
                    $this->out($label . $this->ok($file));
                } else {
                    $this->out($label . $this->fail($file));
                    $this->_flagFailing();
                }
            } else {
                $this->out($label . $this->skip($file));
            }
        }

        $this->nl();
    }

    protected function _flagFailing()
    {
        $this->_seemsFine = false;
    }

    protected function _uninstallDone()
    {
        $this->nl();
        if ($this->_seemsFine) {
            $this->out("========================================================================");
            $this->nl();
            $this->out("                      Uninstallation completed!");
            $this->out("             Please remember to remove the composer dependency.");
            $this->nl();
            $this->out("========================================================================");
        } else {
            $this->out("Automatic uninstallation failed to complete cleanly.");
            $this->out("This is often due to directories not being empty.");
            $this->out("Based on the output above, you can delete the directories manually.");
        }
        $this->nl();
    }

    protected function _installDone()
    {
        $this->nl();
        if ($this->_seemsFine) {
            $this->out("========================================================================");
            $this->nl();
            $this->out("                      Installation completed!");
            $this->out("              Run '".$this->info('bin/strata server')."' to start your app.");
            $this->nl();
            $this->out("========================================================================");
        } else {
            $this->out("Automatic installation failed to complete cleanly.");
            $this->out("Head over to https://github.com/francoisfaubert/wordpress-mvc/ to get help.");
        }
        $this->nl();
    }

}
