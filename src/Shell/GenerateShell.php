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
    
    public function contextualize($args)
    {
        if (count($args) > 2) {
            switch($args[2]) {
                case "controller" :                
                    $this->_type = "controller";
                    $this->_classname = Inflector::classify($args[3]);
                    break;
                case "model" :
                    $this->_type = "model";
                    $this->_classname = Inflector::classify($args[3]);
                    $this->_options["is_ctp"] =  count($args) > 4 && $args[4] == "true";
                    break;
                                
                case "helper" :
                    $this->_type = "helper";
                    $this->_classname = Inflector::classify($args[3]);   
                    break;
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
        switch ($this->_type) {
            case "controller" : return $this->_renderController();
            case "model" : return $this->_renderModel();
            case "helper" : return $this->_renderHelper();
        }        
    }
    
    protected function _renderController()
    {
        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . $this->_classname . ".php", "<?php
            namespace {{NAMESPACE}}\Controller;
            
            class {$this->_classname} extends {{NAMESPACE}}\Controller\AppController {
            
            
            }
        ");
                
        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . "Test" . $this->_classname . ".php", "<?php
            namespace {{NAMESPACE}}\Tests\Controller;
            
            class Test{$this->_classname} extends \MVC\Test\Test {
                        
            }
        ");
    }
    
    protected function _renderModel()
    {
        $ctpExtend =  (bool)$this->_options["is_ctp"] ? "\MVC\CustomPostTypes\Entity" : "{{NAMESPACE}}\Model\AppModel";
        
        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR . $this->_classname . ".php", "<?php
            namespace {{NAMESPACE}}\Model;
            
            class {$this->_classname} extends $ctpExtend {
            
            
            }
        ");
                
        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR . "Test" . $this->_classname . ".php", "<?php
            namespace {{NAMESPACE}}\Tests\Model;
            
            class Test{$this->_classname} extends \MVC\Test\Test {
                        
            }
        ");
    }    
    
    protected function _renderHelper()
    {        
        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . "helper" . DIRECTORY_SEPARATOR. $this->_classname . ".php", "<?php
            namespace {{NAMESPACE}}\View\Helper;
            
            class {$this->_classname} extends {{NAMESPACE}}\View\Helper\AppHelper {
            
            
            }
        ");
                
        file_put_contents(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . "helper" . DIRECTORY_SEPARATOR. "Test" . $this->_classname . ".php", "<?php
            namespace {{NAMESPACE}}\Tests\View\Helper;
            
            class Test{$this->_classname} extends \MVC\Test\Test {
                        
            }
        ");
    }  
}
