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
        $this->out('');
        $this->startup();
        $this->out('');

        $this->out('A webserver is now availlable at http://127.0.0.1:3000/');
        $this->out('Press CTRL + C to exit');

        $command = "tail -f /vagrant/log/access.log";
        system("vagrant ssh -c '" . $command . "'");
        system("vagrant suspend");
    }
}
