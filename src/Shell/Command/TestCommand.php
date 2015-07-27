<?php
namespace Strata\Shell\Command;

use Strata\Strata;
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
        $arguments = $this->_preparePhpunitArguments();
        system(sprintf("php %s %s %s", $phpunit, $arguments, Strata::getTestPath()));

        $this->shutdown();
    }


    /**
     * Return the path to the Apigen binary
     * @return string Apigen binary path
     */
    protected function _getPhpunitBin()
    {
        return "vendor/bin/phpunit";
    }

    protected function _preparePhpunitArguments()
    {
        $arguments = array("--colors");

        if ($this->_hasBootstrapFile()) {
            $arguments[] = "--bootstrap " . $this->_getBootstrapFile();
        } else {
            $arguments[] = "--bootstrap " . Strata::getVendorPath() . "autoload.php";
        }

        return implode(" ", $arguments);
    }

    private function _hasBootstrapFile()
    {
        return file_exists($this->_getBootstrapFile());
    }

    protected function _getBootstrapFile()
    {
        return implode(DIRECTORY_SEPARATOR, array(Strata::getTestPath() . "strata-test-bootstraper.php"));
    }
}
