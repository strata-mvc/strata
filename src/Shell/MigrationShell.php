<?php
/**
 */
namespace MVC\Shell;

/**
 * built-in Migration Shell
 */
class MigrationShell extends \MVC\Shell\Shell
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
        if (is_null($this->_config['filepath'])) {
            $this->out('No file passed as migration. Loading most recent .sql file in /db/.');
            $this->_config['filepath'] = $this->_getMostRecent(MVC_ROOT_PATH . DIRECTORY_SEPARATOR . "db");
            $this->out('Applying migration for ' . $this->_config['filepath']);
        } else {
            $this->out('Applying migration for ' . $this->_config['filepath']);
        }

        $this->out('');
        $this->startup();
        $this->out('');

        $command = sprintf("pv %s | mysql -u%s -p%s %s", "/vagrant/db/" . $this->_config['filepath'], DB_USER, DB_PASSWORD, DB_NAME);
        system("vagrant ssh -c '" . $command . "'");
        system("vagrant suspend");
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
