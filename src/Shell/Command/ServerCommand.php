<?php

namespace Strata\Shell\Command;

use Strata\Shell\Command\StrataCommandBase;
use Strata\Strata;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Starts the Strata Server Shell. This server is expected to understand the differences in
 * configuration between a Vagrant environment and cases when it runs using the current
 * computer's binaries.
 *
 * Intended use is <code>./strata server</code>
 */
class ServerCommand extends StrataCommandBase
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('server')
            ->setDescription('Starts a server instance');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        $output->writeln('A webserver is now available at <info>http://127.0.0.1:5454/</info>');

        $this->nl();

        $command = "WP_ENV=development php -S 0.0.0.0:5454 -t web/";

        if ($this->hasIniFile()) {
            $output->writeln('Using found <info>php.ini</info> file.');
            $command .= " -c php.ini";
        }

        $output->writeln('Press <info>CTRL + C</info> to exit');

        system($command);

        $this->shutdown();
    }

    /**
     * Confirms whether $filename exists at the base
     * of the project.
     * @return boolean
     */
    private function hasIniFile($filename = "php.ini")
    {
        return file_exists(Strata::getRootPath() . DIRECTORY_SEPARATOR . $filename);
    }
}
