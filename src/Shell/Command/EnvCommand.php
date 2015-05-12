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
        "tmp"
    );


    /**
     * The source URL for stater app files
     *
     * @todo This needs to be a composer dependency.
     * @var string
     */
    protected $_srcUrl = "https://raw.githubusercontent.com/francoisfaubert/strata-template-files/master/src/";

    /**
     * Strata's empty project files and their destination.
     *
     * @var array
     */
    protected $_starterFiles = array(
        'AppController.php' => 'src/controller/AppController.php',
        'AppModel.php'      => 'src/model/AppModel.php',
        'AppHelper.php'     => 'src/view/helper/AppHelper.php',
        'strata-bootstraper.php' => 'web/app/mu-plugins/strata-bootstraper.php',
    );

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('env')
            ->setDescription('Manages the Strata installation on your Bedrock Wordpress stack')
            ->addArgument(
                'mode',
                InputArgument::REQUIRED,
                'One of install or uninstall.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        switch ($input->getArgument('mode')) {
            case "install":
                $this->_install();
                break;
            case "uninstall":
                $this->_uninstall();
                break;
            default : throw new InvalidArgumentException("That is not a valid command.");
        }

        $this->shutdown();
    }

    /**
     * Installs Strata files and directories
     * @return null
     */
    protected function _install()
    {
        $this->_createDirectoryStructure();
        $this->_createStarterFiles();
        $this->_installDone();
    }

    /**
     * Uninstalls Strata files and directories
     * @return null
     */
    protected function _uninstall()
    {
        $this->_removeStarterFiles();
        $this->_removeDirectoryStructure();
        $this->_uninstallDone();
    }

    /**
     * Creates the directory structure. Does not overwrite existing
     * directories.
     * @return null
     */
    protected function _createDirectoryStructure()
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
                    $this->_flagFailing();
                }
            } else {
                $this->output->writeLn($label . $this->skip($dir));
            }
        }

        $this->nl();
    }

    /**
     * Removes the directory structure. Does not delete non-empty
     * directories.
     * @return null
     */
    protected function _removeDirectoryStructure()
    {
        $this->output->writeLn("Attempting to remove directory structure.");

        $count = 0;
        foreach (array_reverse($this->_directoryStructure) as $dir) {
            $label = $this->tree(++$count >= count($this->_directoryStructure));
            if (is_dir($dir)) {
                if (@rmdir($dir)) {
                    $this->output->writeLn($label . $this->ok($dir));
                } else {
                    $this->output->writeLn($label . $this->fail($dir). " <fg=yellow>Is it empty?</fg=yellow>");
                    $this->_flagFailing();
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
    protected function _createStarterFiles()
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
                    $this->_flagFailing();
                }
            } else {
                $this->output->writeLn($label . $this->skip($file));
            }
        }

        $this->nl();

        $file = "bin/phpunit.phar";
        $this->output->writeLn("Fetching PHPUnit");

         if (!file_exists($file)) {
            if (file_put_contents($file, fopen("https://phar.phpunit.de/phpunit.phar", 'r')) > 0) {
                $this->output->writeLn($label . $this->ok($file));
            } else {
                $this->output->writeLn($label . $this->fail($file));
                $this->_flagFailing();
            }
        } else {
            $this->output->writeLn($label . $this->skip($file));
        }

        $this->nl();
    }

    /**
     * Removes the starter file from a new project.
     * @return null
     */
    protected function _removeStarterFiles()
    {
        $this->output->writeLn("Attempting to remove starter files.");

        $count = 0;
        foreach ($this->_starterFiles as $file) {
            $label = $this->tree(++$count >= count($this->_starterFiles));
            if (file_exists($file)) {
                if (unlink($file)) {
                    $this->output->writeLn($label . $this->ok($file));
                } else {
                    $this->output->writeLn($label . $this->fail($file));
                    $this->_flagFailing();
                }
            } else {
                $this->output->writeLn($label . $this->skip($file));
            }
        }

        $this->nl();
    }

    /**
     * Flag the current process is not behaving as we expected.
     * @return null
     */
    protected function _flagFailing()
    {
        $this->_seemsFine = false;
    }

    /**
     * Presents a summary of the operation to the user.
     * @return
     */
    protected function _uninstallDone()
    {
        $this->nl();
        if ($this->_seemsFine) {
            $this->output->writeLn("========================================================================");
            $this->nl();
            $this->output->writeLn("                      Uninstallation completed!");
            $this->output->writeLn("             Please remember to remove the composer dependency.");
            $this->nl();
            $this->output->writeLn("========================================================================");
        } else {
            $this->output->writeLn("Automatic uninstallation failed to complete cleanly.");
            $this->output->writeLn("This is often due to directories not being empty.");
            $this->output->writeLn("Based on the output above, you can delete the directories manually.");
        }
        $this->nl();
    }

    /**
     * Presents a summary of the operation to the user.
     * @return
     */
    protected function _installDone()
    {
        $this->nl();
        if ($this->_seemsFine) {
            $this->output->writeLn("========================================================================");
            $this->nl();
            $this->output->writeLn("                      Installation completed!");
            $this->output->writeLn("              Run '<info>bin/strata server</info>' to start your app.");
            $this->nl();
            $this->output->writeLn("========================================================================");
        } else {
            $this->output->writeLn("Automatic installation failed to complete cleanly.");
            $this->output->writeLn("Head over to https://github.com/francoisfaubert/strata/ to get help.");
        }
        $this->nl();
    }
}
