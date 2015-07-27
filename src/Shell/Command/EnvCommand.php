<?php
namespace Strata\Shell\Command;

use Strata\Shell\Command\StrataCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

/**
 * Automates Strata self-maintaining scripts.
 *
 * Intended use include:
 *     <code>bin/strata strata install</code>
 *     <code>bin/strata strata uninstall</code>
 */
class EnvCommand extends StrataCommand
{
    /**
     * A flag that is maintain through a process to advise the
     * user should something happen.
     *
     * @var boolean
     */
    protected $_seemsFine = true;

    /**
     * Strata's directory structure
     *
     * @var array
     */
    protected $_directoryStructure = array(
        "bin",
        "config",
        "db",
        "doc",
        "log",
        "src",
        "src/Controller",
        "src/Model",
        "src/Model/Validator",
        "src/View",
        "src/View/helper",
        "test",
        "test/Controller",
        "test/Model",
        "test/Model/Validator",
        "test/View",
        "test/View/Helper",
        "test/Fixture",
        "test/Fixture/Wordpress",
        "tmp"
    );

    /**
     * The source URL for stater app files
     *
     * @todo This needs to be a composer dependency.
     * @var string
     */
    protected $_srcUrl = "https://raw.githubusercontent.com/francoisfaubert/strata-env/master/";

    /**
     * Strata's empty project files and their destination.
     *
     * @var array
     */
    protected $_starterFiles = array(
        'src/Controller/AppController.php' => 'src/Controller/AppController.php',
        'src/Model/AppModel.php'      => 'src/Model/AppModel.php',
        'src/Model/AppCustomPostType.php'      => 'src/Model/AppCustomPostType.php',
        'src/View/Helper/AppHelper.php'     => 'src/View/Helper/AppHelper.php',
        'config/strata.php'        => 'config/strata.php',
        'web/app/mu-plugins/strata-bootstraper.php'        => 'web/app/mu-plugins/strata-bootstraper.php',
        'test/strata-test-bootstraper.php'   => 'test/strata-test-bootstraper.php',
        'test/Fixture/Wordpress/wordpress-bootstraper.php'     => 'test/Fixture/Wordpress/wordpress-bootstraper.php',
    );

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('env')
            ->setDescription('Manages the Strata installation.')
            ->addArgument(
                'mode',
                InputArgument::REQUIRED,
                'repair'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        switch ($input->getArgument('mode')) {
            case "repair":
                $this->repair();
                break;
            default : throw new InvalidArgumentException("That is not a valid command.");
        }

        $this->shutdown();
    }

    /**
     * Installs Strata files and directories
     * @return null
     */
    protected function repair()
    {
        $this->createDirectoryStructure();
        $this->createStarterFiles();
        $this->installDone();
    }

    /**
     * Creates the directory structure. Does not overwrite existing
     * directories.
     * @return null
     */
    protected function createDirectoryStructure()
    {
        $this->output->writeLn("Ensuring correct directory structure.");

        $count = 0;
        foreach ($this->_directoryStructure as $dir) {
            $label = $this->tree(++$count >= count($this->_directoryStructure));
            if (!is_dir($dir)) {
                if (mkdir($dir)) {
                    $this->output->writeLn($label . $this->ok($dir));
                } else {
                    $this->output->writeLn($label . $this->fail($dir));
                    $this->flagFailing();
                }
            } else {
                $this->output->writeLn($label . $this->skip($dir));
            }
        }

        $this->nl();
    }


    /**
     * Adds the starter file to a new project. Does not overwrite existing
     * files.
     * @return null
     */
    protected function createStarterFiles()
    {
        $this->output->writeLn("Ensuring project files are present.");

        $count = 0;
        foreach ($this->_starterFiles as $source => $file) {
            $label = $this->tree(++$count >= count($this->_starterFiles));
            if (!file_exists($file)) {
                if (file_put_contents($file, fopen($this->_srcUrl . $source, 'r')) > 0) {
                    $this->output->writeLn($label . $this->ok($file));
                } else {
                    $this->output->writeLn($label . $this->fail($file));
                    $this->flagFailing();
                }
            } else {
                $this->output->writeLn($label . $this->skip($file));
            }
        }

        $this->nl();
    }


    protected function _getPhpunit()
    {
        $file = "bin/phpunit.phar";
        $label = $this->tree(true);
        $this->output->writeLn("Fetching PHPUnit");

         if (!file_exists($file)) {
            if (file_put_contents($file, fopen("https://phar.phpunit.de/phpunit.phar", 'r')) > 0) {
                $this->output->writeLn($label . $this->ok($file));
            } else {
                $this->output->writeLn($label . $this->fail($file));
                $this->flagFailing();
            }
        } else {
            $this->output->writeLn($label . $this->skip($file));
        }

        $this->nl();
    }


    /**
     * Flag the current process is not behaving as we expected.
     * @return null
     */
    protected function flagFailing()
    {
        $this->_seemsFine = false;
    }


    /**
     * Presents a summary of the operation to the user.
     * @return
     */
    protected function installDone()
    {
        $this->nl();
        if ($this->_seemsFine) {
            $this->output->writeLn("========================================================================");
            $this->nl();
            $this->output->writeLn("                      Repair completed!");
            $this->nl();
            $this->output->writeLn("========================================================================");
        } else {
            $this->output->writeLn("Automatic installation failed to complete cleanly.");
            $this->output->writeLn("Head over to https://github.com/francoisfaubert/strata/ to get help.");
        }
        $this->nl();
    }
}
