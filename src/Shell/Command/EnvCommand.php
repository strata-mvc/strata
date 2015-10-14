<?php
namespace Strata\Shell\Command;

use Strata\Shell\Command\StrataCommand;
use Strata\Strata;

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
 *     <code>./strata env repair</code>
 *     <code>./strata env psr4format</code>
 */
class EnvCommand extends StrataCommand
{
    /**
     * A flag that is maintain through a process to advise the
     * user should something happen.
     *
     * @var boolean
     */
    protected $seemsFine = true;

    /**
     * Strata's directory structure
     *
     * @var array
     */
    protected $directoryStructure = array(
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
     * @var  string
     */
    protected $srcUrl = "https://raw.githubusercontent.com/francoisfaubert/strata-env/master/";

    /**
     * Strata's empty project files and their destination.
     *
     * @var array
     */
    protected $starterFiles = array(
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
                'action',
                InputArgument::REQUIRED,
                'The action name. One of: "repair", "psr2format"'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        switch ($input->getArgument('action')) {
            case "repair":
                $this->repair();
                break;
            case "psr2format":
                $this->psr2format();
                break;
            default:
                throw new InvalidArgumentException("That is not a valid command.");
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
     * Formats Strata files in the PSR2 format
     * using
     * @return [type] [description]
     */
    protected function psr2format()
    {
        $phpcbf = 'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phpcbf';

        if (!file_exists($phpcbf)) {
            throw new InvalidArgumentException("phpcbf is not present in the vendor directory. Are all the require-dev packages still present?");
        }

        system(sprintf("./%s -w src --standard=PSR2", $phpcbf));
        system(sprintf("./%s -w test --standard=PSR2", $phpcbf));
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
        foreach ($this->directoryStructure as $dir) {
            $label = $this->tree(++$count >= count($this->directoryStructure));
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
        foreach ($this->starterFiles as $source => $file) {
            $label = $this->tree(++$count >= count($this->starterFiles));
            if (!file_exists($file)) {
                if (file_put_contents($file, fopen($this->srcUrl . $source, 'r')) > 0) {
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

    /**
     * Flag the current process is not behaving as we expected.
     * @return null
     */
    protected function flagFailing()
    {
        $this->seemsFine = false;
    }

    /**
     * Presents a summary of the operation to the user.
     *
     * @return
     */
    protected function installDone()
    {
        $this->nl();
        if ($this->seemsFine) {
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
