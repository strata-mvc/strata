<?php
/**
 */
namespace Strata\Shell;

/**
 * built-in Migration Shell
 */
class MigrationShell extends \Strata\Shell\Shell
{
    public function initialize($options = array())
    {
        $this->_config = $options + array(
            "filepath"  => null
        );

        parent::initialize($options);
    }

    public function contextualize($args)
    {
        if (count($args) > 2) {
            $this->_config["filepath"] = $args[2];
        }

        parent::contextualize($args);
    }

    /**
     * Override main() to handle action
     *
     * @return void
     */
    public function main()
    {
        $this->out('');
        $this->startup();
        $this->out('');

        if (is_null($this->_config['filepath'])) {
            $this->out('No file passed as migration. Loading most recent .sql file in /db/.');
            $this->_config['filepath'] = $this->_getMostRecent(Strata_ROOT_PATH . DIRECTORY_SEPARATOR . "db");
            $this->out('Applying migration for ' . $this->_config['filepath']);
        } else {
            $this->out('Applying migration for ' . $this->_config['filepath']);
        }

        $command = sprintf("pv %s | mysql -u%s -p%s %s", "/vagrant/db/" . $this->_config['filepath'], DB_USER, DB_PASSWORD, DB_NAME);
        system($command);
        $this->shutdown();
    }

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
