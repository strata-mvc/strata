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
        host and port by calling bin/strata [arg] [arg1] [n].
    */


    /**
     * Override main() to handle action
     *
     * @return void
     */
    public function main()
    {
        $this->startup();

        $this->out('A webserver is now availlable at ' . $this->info("http://127.0.0.1:5454/"));
        $this->out('Press ' . $this->info("CTRL + C") . ' to exit');

        $this->nl();

        // Does this work on vagrant?
        system("php -S 0.0.0.0:5454 -t web/");


        // Tail the server logs in order to keep the illusion that the console is
        // controlling the server.
        //system("tail -n 0 -f /vagrant/log/access.log");


        $this->shutdown();
    }
}
