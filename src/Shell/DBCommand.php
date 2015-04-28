<?php
namespace Strata\Shell;

use Strata\Shell\StrataCommand;

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
class DBCommand extends StrataCommand
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
                new InputDefinition(array(
                    new InputOption('filename', 'f', InputOption::VALUE_OPTIONAL),
                ))
            )
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'One of the following: migrate, import or dump.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        switch ($input->getArgument('type')) {
            case "migrate" :
                $this->_importSqlFile( $this->_getSqlFile() );
                break;

            case "import" :
                $this->_output->writeLn("Importing from an environment is not yet available.");
                break;

            case "dump" :
                $this->_dumpCurrentDB();
                break;
        }

        $this->nl();

        $this->shutdown();
    }

    /**
     * Dumps the current environment's database to an .sql file in /db/
     * @todo Ensure this works in and outside of Vagrant.
     */
    protected function _dumpCurrentDB()
    {
        $relativeFilename = sprintf("db/dump_%s.sql", date('m-d-Y_hia'));
        $absoluteFilename = "\/vagrant\/" . $relativeFilename;
        $command = sprintf("mysqldump -u%s -p%s %s > %s", DB_USER, DB_PASSWORD, DB_NAME, $absoluteFilename);

        $this->_output->writeLn("Generating MySQL export dump to ./$relativeFilename");
        system($command);
    }

    /**
     * Imports an .sql file to the current environment's database.
     * @todo Ensure this works in an outside of Vagrant.
     */
    protected function _importSqlFile($file)
    {
        if (!is_null($file)) {
            $this->_output->writeLn("Applying migration for <info>$file</info>");

            $command = sprintf("pv %s | mysql -u%s -p%s %s", $file, DB_USER, DB_PASSWORD, DB_NAME);
            system($command);
            return;
        }

        $this->_output->writeLn("<error>We could not find a valid SQL file.</error>");
    }

    /**
     * Gets the working .sql file either from an option passed to the command or
     * by returning the most recent sql file in /db/.
     * @return string Filepath
     */
    protected function _getSqlFile()
    {
        if (!is_null($this->_input->getOption('filename'))) {
            return $this->_input->getOption('filename');
        }

        $this->_output->writeLn('No file passed as migration. Loading most recent .sql file in <info>./db/</info>');
        return $this->_getMostRecent(\Strata\Strata::getDbPath());
    }

    /**
     * Returns the most recent file in $path.
     * @param  string $path Where to look
     * @return string       Most recent file
     */
    protected function _getMostRecent($path)
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
        return $latestFilename;
    }
}
