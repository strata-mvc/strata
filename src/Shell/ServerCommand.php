<?php
/**
 */
namespace Strata\Shell;

use Strata\Shell\StrataCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * built-in Server Shell
 */
class ServerCommand extends StrataCommand
{
    protected function configure()
    {
        $this
            ->setName('server')
            ->setDescription('Starts a server instance');
    }


    /**
     * Starts a server instance
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        $output->writeln('A webserver is now availlable at <info>http://127.0.0.1:5454/</info>');
        $output->writeln('Press <info>CTRL + C</info> to exit');

        $this->nl();

        // Does this work on vagrant?
        system("php -S 0.0.0.0:5454 -t web/");

        // Tail the server logs in order to keep the illusion that the console is
        // controlling the server.
        //system("tail -n 0 -f /vagrant/log/access.log");


        $this->shutdown();
    }
}
