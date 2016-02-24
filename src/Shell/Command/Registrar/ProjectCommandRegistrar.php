<?php

namespace Strata\Shell\Command\Registrar;

use Strata\Strata;
use Strata\Shell\Command\StrataCommand;
use Symfony\Component\Console\Application;

/**
 * Registers commands declared at the project level.
 */
class ProjectCommandRegistrar
{
    /**
     * A link to a shell application to which
     * the commands will be added.
     * @var Application
     */
    private $application = null;

    function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Assigns all the declared project commands.
     */
    public function assign()
    {
        $cmdPath = Strata::getCommandPath();
        if (is_dir($cmdPath)) {
            $this->parseDirectoryForCommandFiles($cmdPath);
        }
    }

    /**
     * Parsed the $path for available Strata shell commands
     * @param  string $path
     */
    private function parseDirectoryForCommandFiles($path)
    {
        foreach (glob($path . "*Command.php") as $filename) {
            if (preg_match("/(\w+?Command).php$/", $filename, $matches)) {
                $this->attemptRegistration($matches[1]);
            }
        }
    }

    /**
     * Attempts the registration of a single shell command.
     * @param  string $name
     */
    private function attemptRegistration($name)
    {
        try {
            $this->application->add(StrataCommand::factory($name));
        } catch (Exception $e) {
            echo "Unable to autoload the '$name' command.";
        }
    }
}
