<?php
namespace Strata\Shell\Command\Registrar;

use Strata\Strata;
use Strata\Shell\Command\StrataCommand;

class ProjectCommandRegistrar
{
    private $application = null;
    private $validProjectCommands = array();

    function __construct(\Symfony\Component\Console\Application $application)
    {
        $this->application = $application;
    }

    public function assign()
    {
        $cmdPath = Strata::getCommandPath();
        if (is_dir($cmdPath)) {
            $this->parseDirectoryForCommandFiles($cmdPath);
        }

        return $this->validProjectCommands;
    }

    private function parseDirectoryForCommandFiles($path)
    {
        foreach (glob($path . "*Command.php") as $filename) {
            if (preg_match("/(\w+?Command).php$/", $filename, $matches)) {
                $this->attemptRegistration($matches[1]);
            }
        }
    }

    private function attemptRegistration($name)
    {
        try {
            $this->application->add(StrataCommand::factory($name));
        } catch(Exception $e) {
            echo "Unable to autoload the '$name' command.";
        }
    }

}
