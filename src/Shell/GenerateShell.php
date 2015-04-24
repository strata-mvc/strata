<?php
/**
 */
namespace MVC\Shell;

use MVC\Shell\Shell;
use MVC\Utility\Inflector;
use Exception;

/**
 * built-in Generator Shell
 */
class GenerateShell extends Shell
{
    protected $_type = null;
    protected $_classname = null;
    protected $_options = array();
    protected $_namespace = null;

    protected $_classTemplate = "<?php
namespace {NAMESPACE};

class {CLASSNAME} extends {EXTENDS} {


}";

    public function contextualize($args)
    {
        if (count($args) > 3) {
            switch($args[3]) {
                case "controller" :
                    $this->_type = "controller";
                    $this->_classname = Inflector::classify($args[4]). "Controller";
                    break;

                case "model" :
                    $this->_type = "model";
                    $this->_classname = Inflector::classify($args[4]);
                    $this->_options["is_ctp"] =  count($args) > 4 && $args[5] == "true";
                    break;

                case "taxonomy" :
                    $this->out("Generating a taxonomy is not yet supported, but should be soon.");
                    break;

                case "validator" :
                    $this->out("Generating a validator is not yet supported, but should be soon.");
                    break;

                case "route" :
                    $this->out("Generating a route is not yet supported, but should be soon.");
                    break;

                case "helper" :
                    $this->_type = "helper";
                    $this->_classname = Inflector::classify($args[4]) . "Helper";
                    break;

                case "migration" :
                    $this->_type = "migration";
                    break;

                default : throw new Exception("That is not a valid command.");
            }
            // offer the option of changing the namespace?
            $this->_namespace = "App";
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
            case "controller"   : $this->_renderController(); break;
            case "model"        : $this->_renderModel(); break;
            case "helper"       : $this->_renderHelper(); break;
            case "migration"    : $this->_exportDB(); break;
        }

        $this->out("");
        $this->shutdown();
    }

    protected function _exportDB()
    {
        $relativeFilename = sprintf("db/dump_%s.sql", date('m-d-Y_hia'));
        $absoluteFilename = "\/vagrant\/" . $relativeFilename;
        $command = sprintf("mysqldump -u%s -p%s %s > %s", DB_USER, DB_PASSWORD, DB_NAME, $absoluteFilename);

        $this->out("Generating MySQL export dump to ./$relativeFilename");
        system($command);
    }

    protected function _generateFileContents($namespace, $classname, $extends)
    {
        $data = $this->_classTemplate;
        $data = str_replace("{EXTENDS}", $extends, $data);
        $data = str_replace("{NAMESPACE}", $namespace, $data);
        $data = str_replace("{CLASSNAME}", $classname, $data);

        return $data;
    }

    protected function _renderController()
    {
        $this->out("Scaffolding controller " . $this->info($this->_classname));


        $destination = implode(DIRECTORY_SEPARATOR, array("src", "controller", $this->_classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$this->_namespace}\Controller", $this->_classname , "\{$this->_namespace}\AppController");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->out($this->tree() . $this->ok($destination));
            } else {
                $this->out($this->tree() . $this->fail($destination));
            }
        } else {
            $this->out($this->tree() . $this->skip($destination));
        }

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "controller", $this->_classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$this->_namespace}\Test\Controller", "Test{$this->_classname}" , "\MVC\Test\Test ");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->out($this->tree(true) . $this->ok($destination));
            } else {
                $this->out($this->tree(true) . $this->fail($destination));
            }
        } else {
            $this->out($this->tree(true) . $this->skip($destination));
        }
    }

    protected function _renderModel()
    {
        $this->out("Scaffolding model " . $this->info($this->_classname));
        $ctpExtend =  (bool)$this->_options["is_ctp"] ? "\MVC\Model\CustomPostType\Entity" : "\{$this->_namespace}\Model\AppModel";

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "model", $this->_classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$this->_namespace}\Model", $this->_classname , $ctpExtend);
            if (@file_put_contents($destination, $contents) > 0) {
                $this->out($this->tree() . $this->ok($destination));
            } else {
                $this->out($this->tree() . $this->fail($destination));
            }
        } else {
            $this->out($this->tree() . $this->skip($destination));
        }

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "model", $this->_classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$this->_namespace}\Test\Model", "Test{$this->_classname}", "\MVC\Test\Test");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->out($this->tree(true) . $this->ok($destination));
            } else {
                $this->out($this->tree(true) . $this->fail($destination));
            }
        } else {
            $this->out($this->tree(true) . $this->skip($destination));
        }
    }

    protected function _renderHelper()
    {
        $this->out("Scaffolding view helper " . $this->info($this->_classname));

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "view", "helper", $this->_classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$this->_namespace}\Test\View\Helper", $this->_classname, "\{$this->_namespace}\View\Helper\AppHelper");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->out($this->tree() . $this->ok($destination));
            } else {
                $this->out($this->tree() . $this->fail($destination));
            }
        } else {
            $this->out($this->tree() . $this->skip($destination));
        }

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "view", "helper", $this->_classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$this->_namespace}\Tests\View\Helper", "Test{$this->_classname}", "\MVC\Test\Test");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->out($this->tree(true) . $this->ok($destination));
            } else {
                $this->out($this->tree(true) . $this->fail($destination));
            }
        } else {
            $this->out($this->tree(true) . $this->skip($destination));
        }
    }
}
