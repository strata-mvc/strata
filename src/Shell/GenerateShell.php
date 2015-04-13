<?php
/**
 */
namespace MVC\Shell;

use MVC\Shell\Shell;
use MVC\Utility\Inflector;

/**
 * built-in Generator Shell
 */
class GenerateShell extends Shell
{
    protected $_type = null;
    protected $_classname = null;
    protected $_options = array();
    protected $_namespace = null;

    public function contextualize($args)
    {
        if (count($args) > 2) {
            switch($args[2]) {

                case "controller" :
                    $this->_type = "controller";
                    $this->_classname = Inflector::classify($args[3]). "Controller";
                    break;

                case "model" :
                    $this->_type = "model";
                    $this->_classname = Inflector::classify($args[3]);
                    $this->_options["is_ctp"] =  count($args) > 4 && $args[4] == "true";
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
                    $this->_classname = Inflector::classify($args[3]) . "Helper";
                    break;

                case "migration" :
                    $this->_type = "migration";
                    break;
            }
            // offer the option of changing the namespace?
            $this->_namespace = MVC_APP_NAMESPACE;
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
        switch ($this->_type) {
            case "controller"   : $this->_renderController(); break;
            case "model"        : $this->_renderModel(); break;
            case "helper"       : $this->_renderHelper(); break;
            case "migration"    : $this->_exportDB(); break;
        }

        $this->out("");
        $this->out("Completed.");
    }

    protected function _exportDB()
    {
        $relativeFilename = sprintf("db/dump_%s.sql", date('m-d-Y_hia'));
        $absoluteFilename = "\/vagrant\/" . $relativeFilename;
        $command = sprintf("mysqldump -u%s -p%s %s > %s", DB_USER, DB_PASSWORD, DB_NAME, $absoluteFilename);

        $this->out("Generating MySQL export dump to ./$relativeFilename");
        system($command);
    }

    protected function _renderController()
    {
        $this->out("Scaffolding controller {$this->_classname}");
        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . $this->_classname . ".php", "<?php
namespace {$this->_namespace}\Controller;

class {$this->_classname} extends \{$this->_namespace}\Controller\AppController {


}
        ");
        $this->out("src" . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . $this->_classname . ".php");

        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . "Test" . $this->_classname . ".php", "<?php
namespace {$this->_namespace}\Tests\Controller;

class Test{$this->_classname} extends \MVC\Test\Test {

}
        ");
        $this->out("tests" . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . "Test" . $this->_classname . ".php");
    }

    protected function _renderModel()
    {
        $this->out("Scaffolding model {$this->_classname}");
        $ctpExtend =  (bool)$this->_options["is_ctp"] ? "\MVC\Model\CustomPostType\Entity" : "\{$this->_namespace}\Model\AppModel";

        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR . $this->_classname . ".php", "<?php
namespace {$this->_namespace}\Model;

class {$this->_classname} extends $ctpExtend {


}
        ");
        $this->out("src" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR . $this->_classname . ".php");

        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR . "Test" . $this->_classname . ".php", "<?php
namespace {$this->_namespace}\Tests\Model;

class Test{$this->_classname} extends \MVC\Test\Test {

}
        ");
        $this->out("tests" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR . "Test" . $this->_classname . ".php");
    }

    protected function _renderHelper()
    {
        $this->out("Scaffolding view helper {$this->_classname}");
        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . "helper" . DIRECTORY_SEPARATOR. $this->_classname . ".php", "<?php
namespace {$this->_namespace}\View\Helper;

class {$this->_classname} extends \{$this->_namespace}\View\Helper\AppHelper {


}
        ");
        $this->out("src" . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . "helper" . DIRECTORY_SEPARATOR. $this->_classname . ".php");

        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . "helper" . DIRECTORY_SEPARATOR. "Test" . $this->_classname . ".php", "<?php
namespace {$this->_namespace}\Tests\View\Helper;

class Test{$this->_classname} extends \MVC\Test\Test {

}
        ");
        $this->out("tests" . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . "helper" . DIRECTORY_SEPARATOR. "Test" . $this->_classname . ".php");
    }
}
