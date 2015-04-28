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
use Exception;

/**
 * Strata self maintaining Shell
 */
class StrataAppCommand extends StrataCommand
{
    protected $_seemsFine = true;

    protected $_directoryStructure = array(
        "bin",
        "bin/vagrant",
        "db",
        "doc",
        "log",
        "src",
        "src/Controller",
        "src/Model",
        "src/View",
        "src/View/helper",
        "test",
        "test/Controller",
        "test/Model",
        "test/View",
        "test/View/Helper",
        "tmp"
    );

    protected $_srcUrl = "https://raw.githubusercontent.com/francoisfaubert/strata-template-files/master/src/";

    protected $_starterFiles = array(
        'AppController.php' => 'src/controller/AppController.php',
        'AppModel.php'      => 'src/model/AppModel.php',
        'AppHelper.php'     => 'src/view/helper/AppHelper.php',
        'strata-bootstraper.php' => 'web/app/mu-plugins/strata-bootstraper.php',
    );


    protected function configure()
    {
        $this
            ->setName('strata')
            ->setDescription('Manages the Strata installation on your Bedrock Wordpress stack')
            ->addOption(
               'install',
               null,
               InputOption::VALUE_NONE,
               'If set, the task will install Strata'
            )
            ->addOption(
               'uninstall',
               null,
               InputOption::VALUE_NONE,
               'If set, the task will uninstall Strata'
            )
        ;
    }


    /**
     *
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        if ($input->getOption('install')) {
            $this->_install();
        } elseif ($input->getOption('uninstall')) {
            $this->_uninstall();
        }

        $this->shutdown();
    }

    protected function _install()
    {
        $this->_createDirectoryStructure();
        $this->_createStarterFiles();
        $this->_installDone();
    }

    protected function _uninstall()
    {
        $this->_removeStarterFiles();
        $this->_removeDirectoryStructure();
        $this->_uninstallDone();
    }

    protected function _createDirectoryStructure()
    {
        $this->_output->writeLn("Ensuring correct directory structure.");

        $count = 0;
        foreach ($this->_directoryStructure as $dir) {
            $label = $this->tree(++$count >= count($this->_directoryStructure));
            if (!is_dir($dir)) {
                if (mkdir($dir)) {
                    $this->_output->writeLn($label . $this->ok($dir));
                } else {
                    $this->_output->writeLn($label . $this->fail($dir));
                    $this->_flagFailing();
                }
            } else {
                $this->_output->writeLn($label . $this->skip($dir));
            }
        }

        $this->nl();
    }

    protected function _removeDirectoryStructure()
    {
        $this->_output->writeLn("Attempting to remove directory structure.");

        $count = 0;
        foreach (array_reverse($this->_directoryStructure) as $dir) {
            $label = $this->tree(++$count >= count($this->_directoryStructure));
            if (is_dir($dir)) {
                if (@rmdir($dir)) {
                    $this->_output->writeLn($label . $this->ok($dir));
                } else {
                    $this->_output->writeLn($label . $this->fail($dir). " <fg=yellow>Is it empty?</fg=yellow>");
                    $this->_flagFailing();
                }
            } else {
                $this->_output->writeLn($label . $this->skip($dir));
            }
        }

        $this->nl();
    }

    protected function _createStarterFiles()
    {
        $this->_output->writeLn("Ensuring project files are present.");

        $count = 0;
        foreach ($this->_starterFiles as $source => $file) {
            $label = $this->tree(++$count >= count($this->_starterFiles));
            if (!file_exists($file)) {
                if (file_put_contents($file, fopen($this->_srcUrl . $source, 'r')) > 0) {
                    $this->_output->writeLn($label . $this->ok($file));
                } else {
                    $this->_output->writeLn($label . $this->fail($file));
                    $this->_flagFailing();
                }
            } else {
                $this->_output->writeLn($label . $this->skip($file));
            }
        }

        $this->nl();
    }

    protected function _removeStarterFiles()
    {
        $this->_output->writeLn("Attempting to remove starter files.");

        $count = 0;
        foreach ($this->_starterFiles as $file) {
            $label = $this->tree(++$count >= count($this->_starterFiles));
            if (file_exists($file)) {
                if (unlink($file)) {
                    $this->_output->writeLn($label . $this->ok($file));
                } else {
                    $this->_output->writeLn($label . $this->fail($file));
                    $this->_flagFailing();
                }
            } else {
                $this->_output->writeLn($label . $this->skip($file));
            }
        }

        $this->nl();
    }

    protected function _flagFailing()
    {
        $this->_seemsFine = false;
    }

    protected function _uninstallDone()
    {
        $this->nl();
        if ($this->_seemsFine) {
            $this->_output->writeLn("========================================================================");
            $this->nl();
            $this->_output->writeLn("                      Uninstallation completed!");
            $this->_output->writeLn("             Please remember to remove the composer dependency.");
            $this->nl();
            $this->_output->writeLn("========================================================================");
        } else {
            $this->_output->writeLn("Automatic uninstallation failed to complete cleanly.");
            $this->_output->writeLn("This is often due to directories not being empty.");
            $this->_output->writeLn("Based on the output above, you can delete the directories manually.");
        }
        $this->nl();
    }

    protected function _installDone()
    {
        $this->nl();
        if ($this->_seemsFine) {
            $this->_output->writeLn("========================================================================");
            $this->nl();
            $this->_output->writeLn("                      Installation completed!");
            $this->_output->writeLn("              Run '<info>bin/strata server</info>' to start your app.");
            $this->nl();
            $this->_output->writeLn("========================================================================");
        } else {
            $this->_output->writeLn("Automatic installation failed to complete cleanly.");
            $this->_output->writeLn("Head over to https://github.com/francoisfaubert/wordpress-mvc/ to get help.");
        }
        $this->nl();
    }

}
