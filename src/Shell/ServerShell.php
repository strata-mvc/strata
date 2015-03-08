<?php
/**
 */
namespace MVC\Shell;

use MVC\Shell\Shell;

/**
 * built-in Server Shell
 */
class ServerShell extends Shell
{
    public function initialize($options = array())
    {
        $this->_config = $options + array(
            "host" => "localhost",
            "port" => "3000",
            "webroot" => MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "webroot"
        );
    }

    public function contextualize($args)
    {
        if (count($args) > 2) {
            for($i = 2; $i < count($args); $i++) {
                $matches = null;
                if (preg_match('/host=(.*)/', $args[$i], $matches)) {
                    $this->_config["host"] = $matches[1];
                }                
                elseif (preg_match('/port=(.*)/', $args[$i], $matches)) {
                    $this->_config["port"] = $matches[1];
                }
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
        $command = sprintf(
            "%s -S %s:%d -t %s %s",
            $this->getPHPBin(),
            $this->_config["host"],
            $this->_config["port"],
            escapeshellarg($this->_config["webroot"]),
            escapeshellarg($this->_config["webroot"] . '/index.php')
        );

        $this->out('');
        $this->out(sprintf('built-in server is running in http://%s%s/', $this->_config["host"], ':' . $this->_config["port"]));
        $this->out('You can exit with `CTRL-C`');        
        $this->out('');
        
        system($command);
    }
}
