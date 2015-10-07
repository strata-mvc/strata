<?php
namespace Strata\Shell\Command\Generator;

use Strata\Strata;
use Strata\Utility\Hash;

class RouteGenerator extends GeneratorBase
{

    private $type;
    private $url;
    private $destination;

    public function configure($options)
    {
        if (count($options) < 3) {
            throw new InvalidArgumentException("A route requires 3 parameters: The request type, the url to match and the MVC destination.");
        }

        $this->type = $options[0];
        $this->url = $options[1];
        $this->destination = $options[2];
    }

    /**
     * Creates a route entry
     * @return null
     */
    public function generate()
    {
        $this->command->output->writeLn("Add new route to <info>{$this->destination}</info>");

        $target = explode("#", $this->destination);
        $controllerName = Strata::getNamespace() . "\\Controller\\" . $target[0];

        if (!class_exists($controllerName)) {
            $this->command->output->writeLn($this->command->tree(true) . $this->command->fail('No file matched the controller handled by this route. Looked for ' . $controllerName. '.'));
            return;
        }

        if (count($target) > 1 && !method_exists($controllerName, $target[1])) {
            $this->command->output->writeLn($this->command->tree(true) . $this->command->fail('No method named '. $target[1] .' is declared by '. $controllerName .'.'));
            return;
        }

        // This is not very robust...

        $line = sprintf("array(\"%s\",\t\"%s\",\t\"%s\"),", strtoupper($this->type), $this->url, $this->destination);
        $contents = file_get_contents(Strata::getProjectConfigurationFilePath());
        if (preg_match("/\"routes\".*?array\(/", $contents, $matches)) {
            $contents = preg_replace("/\"routes\".*?array\(/", $matches[0] . "\n\t\t" . $line, $contents);
        }
        if (file_put_contents(Strata::getProjectConfigurationFilePath(), $contents)) {
            $this->command->output->writeLn($this->command->tree(true) . $this->command->ok("Added route : {$this->type} | {$this->url} -> {$this->destination}."));
        }
    }
}
