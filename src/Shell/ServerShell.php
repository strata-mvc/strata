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
            "host"  => "0.0.0.0", // Allow outside access
            "port"  => "8080",
            "webroot" => "/srv/www/webroot"
        );

        parent::initialize($options);
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
        // $command = sprintf(
        //     "%s -S %s:%d -t %s %s",
        //     $this->getPHPBin(),
        //     $this->_config["host"],
        //     $this->_config["port"],
        //     escapeshellarg($this->_config["webroot"]),
        //     escapeshellarg($this->_config["webroot"] . '/index.php')
        // );

        $this->out('');
        $this->startup();
        $this->out('');

        $this->out('A webserver is now availlable at http://192.168.33.10/');
        $this->out('Press CTRL + C to exit');

        $command = "sudo tail -f /var/log/apache2/access.log";
        system("vagrant ssh -c '" . $command . "'");
        system("vagrant suspend");
    }
}
