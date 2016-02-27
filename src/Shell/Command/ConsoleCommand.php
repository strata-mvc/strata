<?php

namespace Strata\Shell\Command;

use Strata\Shell\Command\StrataCommandBase;
use Psy\Shell as PsyShell;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Starts the Strata Console Shell. This console is expected to debug
 * a project code from the command line.
 *
 * Intended use is <code>./strata console</code>
 */
class ConsoleCommand extends StrataCommandBase
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('console')
            ->setDescription('Starts a console instance');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        $output->writeln('Press <info>CTRL + C</info> or type <info>exit</info> to quit');
        $this->nl();

        $psy = new PsyShell();
        $psy->run();
    }
}
