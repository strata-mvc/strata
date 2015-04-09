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
    /*
        The original idea here was to allow the user to modify the
        host and port by calling bin/mvc [arg] [arg1] [n].

        However, because we needed to use a Vagrant VM this options
        is currently on hold
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
     */

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

        $this->out('A webserver is now availlable at http://127.0.0.1:5454/');
        $this->out('Press CTRL + C to exit');
        $this->out('');

        // Tail the server logs in order to keep the illusion that the console is
        // controlling the server.
        system("tail -f /vagrant/log/error.log -f /vagrant/log/access.log");
        $this->shutdown();
    }
}
