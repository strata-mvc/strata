<?php

namespace Strata\Shell\Command;

use Strata\Strata;
use Strata\Shell\Command\StrataCommandBase;

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
class TestCommand extends StrataCommandBase
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

        $phpunit = $this->getPhpunitBin();
        $arguments = $this->preparePhpunitArguments();

        $call = sprintf("%s %s %s",
            $this->getPhpunitBin(),
            $this->preparePhpunitArguments(),
            Strata::getTestPath()
        );
        system($call);

        $this->shutdown();
    }

    /**
     * Returns the path to the phpunit binary
     * @return string
     */
    protected function getPhpunitBin()
    {
        return "vendor/bin/phpunit";
    }

    /**
     * Prepares the arguments that can be sent to phpunit
     * @return string
     */
    protected function preparePhpunitArguments()
    {
        $arguments = array("--colors");

        if ($this->hasBootstrapFile()) {
            $arguments[] = "--bootstrap " . $this->getBootstrapFile();
        } else {
            $arguments[] = "--bootstrap " . Strata::getVendorPath() . "autoload.php";
        }

        $arguments[] = sprintf("--log-junit %s/unittest/strata_test_results.xml", Strata::getTmpPath());

        return implode(" ", $arguments);
    }

    /**
     * Confirms the presence of a phpunit bootstrap file
     * @return boolean
     */
    private function hasBootstrapFile()
    {
        return file_exists($this->getBootstrapFile());
    }

    /**
     * Returns the path to where phpunit's bootstrap file should be.
     * @return string
     */
    protected function getBootstrapFile()
    {
        $path = array(Strata::getVendorPath(), 'francoisfaubert', 'strata', 'src', 'Scripts', 'test_runner.php');
        return implode(DIRECTORY_SEPARATOR, $path);
    }
}
