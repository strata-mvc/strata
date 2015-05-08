<?php
namespace Strata\Shell\Command;

use Strata\Shell\Command\StrataCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Starts the project's test suite.
 *
 * Intended use is <code>bin/strata test</code>
 */
class TestCommand extends StrataCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Runs the test suite');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        $output->writeln('Starting tests');
        $this->nl();

        $phpunit = $this->_getPhpunitBin();
        system("php $phpunit --colors --bootstrap vendor/autoload.php test");

        $this->shutdown();
    }


    /**
     * Return the path to the Apigen binary
     * @return string Apigen binary path
     */
    protected function _getPhpunitBin()
    {
        return implode(DIRECTORY_SEPARATOR, array(\Strata\Strata::getOurVendorPath() . "vendor", "phpunit", "phpunit", "phpunit"));;
    }
}
