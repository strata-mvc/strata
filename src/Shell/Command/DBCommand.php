<?php

namespace Strata\Shell\Command;

use Strata\Strata;
use Strata\Shell\Command\StrataCommandBase;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Automates Strata's database manipulation operations.
 *
 * Intended use include:
 *     <code>bin/strata db migrate</code>
 *     <code>bin/strata db import</code>
 *     <code>bin/strata db dump</code>
 */
class DBCommand extends StrataCommandBase
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('db')
            ->setDescription('Executes SQL migrations.')
            ->setDefinition(
                new InputDefinition(
                    array(
                    new InputOption('filename', 'f', InputOption::VALUE_OPTIONAL),
                    )
                )
            )
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'One of the following: create, migrate or export.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        switch ($input->getArgument('type')) {
            case "create":
                $this->createDB();
                break;

            case "migrate":
                $this->importSqlFile($this->getSqlFile());
                break;

            case "export":
                $this->dumpCurrentDB();
                break;

            default : throw new InvalidArgumentException("This is not a valid argument for this command.");
        }

        $this->nl();
        $this->shutdown();
    }

    /**
     * Creates the project's database.
     */
    protected function createDB()
    {
        $this->output->writeLn("Generating MySQL database based on the environment's configuration.");
        $command = sprintf("%s db create", $this->getWpCliPath());
        system($command);

        $this->nl();

        $title = 'StrataEnvironment';
        $adminUser = "admin";
        $adminPassword = "admin";
        $adminEmail = "admin@yourdomain.com";

        $this->output->writeLn("Installing using the following administrator information:");
        $this->output->writeLn("  Title:      $title");
        $this->output->writeLn("  User:       $adminUser");
        $this->output->writeLn("  Password:   $adminPassword");
        $this->output->writeLn("  Email:      $adminEmail");

        $command = sprintf("%s core install --url=%s --title=%s --admin_user=%s --admin_password=%s --admin_email=%s", $this->getWpCliPath(), WP_HOME, $title, $adminUser, $adminPassword, $adminEmail);
        system($command);
    }

    /**
     * Returns the path of wp-cli within the project.
     * @return string
     */
    protected function getWpCliPath()
    {
        $filename = sprintf("%sbin/wp", Strata::getVendorPath());

        if (!is_executable($filename) && !chmod($filename, 755)) {
            $this->output->writeLn(sprintf("Could not grant execute permissions to %s. Command will fail.", $filename));
        }

        return $filename;
    }

    /**
     * Dumps the current environment's database to an .sql file in /db/
     * @todo Ensure this works in and outside of Vagrant.
     */
    protected function dumpCurrentDB()
    {
        date_default_timezone_set("Etc/UTC");
        $relativeFilename = sprintf("export_%s_%s.sql", date('m-d-Y'), time());
        $command = sprintf("%s db export %s%s --add-drop-table", $this->getWpCliPath(), Strata::getDbPath(), $relativeFilename);
        $this->output->writeLn("Generating MySQL export dump to ./$relativeFilename");
        system($command);
    }

    /**
     * Imports an .sql file to the current environment's database.
     * @todo Ensure this works in an outside of Vagrant.
     */
    protected function importSqlFile($file)
    {
        if (!is_null($file)) {
            $this->output->writeLn("Applying migration for <info>$file</info>");
            $command = sprintf("%s db import %s", $this->getWpCliPath(), $file);
            system($command);
            return;
        }

        $this->output->writeLn("<error>We could not find a valid SQL file.</error>");
    }

    /**
     * Gets the working .sql file either from an option passed to the command or
     * by returning the most recent sql file in /db/.
     * @return string Filepath
     */
    protected function getSqlFile()
    {
        if (!is_null($this->input->getOption('filename'))) {
            return $this->input->getOption('filename');
        }

        throw new Exception('No file passed as migration. Add it using the -f option.');
    }

    /**
     * Returns the most recent file in $path.
     * @param  string $path Where to look
     * @return string       Most recent file
     */
    protected function getMostRecent($path)
    {
        $latestFilename = null;
        $latestCtime = 0;

        $d = dir($path);
        while (false !== ($entry = $d->read())) {
            $filepath = "{$path}/{$entry}";
            // could do also other checks than just checking whether the entry is a file
            if (is_file($filepath) && filectime($filepath) > $latestCtime) {
                $latestFilename = $entry;
            }
        }
        return $path . $latestFilename;
    }
}
