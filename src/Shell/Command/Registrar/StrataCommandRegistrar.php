<?php

namespace Strata\Shell\Command\Registrar;

use Strata\Strata;
use Strata\Shell\Command\StrataCommandNamer;
use Symfony\Component\Console\Application;

/**
 * Registers commands declared at Strata's level.
 */
class StrataCommandRegistrar
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
        $path = array(Strata::getOurVendorPath(), "src", "Shell", "Command");
        $cmdPath = implode($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

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
        foreach (glob($path . "*". StrataCommandNamer::getClassNameSuffix() .".php") as $filename) {
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
            $classpath = implode(array(
                "Strata",
                StrataCommandNamer::getNamespaceStringInStrata(),
                $name
            ), "\\");

            $this->application->add(new $classpath());
        } catch (Exception $e) {
            echo "Unable to autoload the '$name' command.";
        }
    }
}
