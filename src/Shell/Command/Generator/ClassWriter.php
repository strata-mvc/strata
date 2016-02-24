<?php

namespace Strata\Shell\Command\Generator;

use Strata\Shell\Command\StrataCommand;

/**
 * Generates classes files within Strata.
 */
class ClassWriter
{
    /**
     * @var string The base string template for creating empty class files.
     */
    private $classTemplate = "<?php
namespace {NAMESPACE};
{USES}
class {CLASSNAME} extends {EXTENDS}
{

{CONTENTS}

}
";

    /**
     * @var string The namespace of the class
     */
    private $namespace;

    /**
     * @var string The actual class short name
     */
    private $classname;

    /**
     * @var string The class being extended
     */
    private $extends;

    /**
     * @var string The contents of the file
     */
    private $contents = '';

    /**
     * @var string The class' dependencies.
     */
    private $uses = '';

    /**
     * @var string The generated file name
     */
    private $destination;

    /**
     * Sets the class' namespace
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Sets the class' class name
     * @param string $classname
     */
    public function setClassname($classname)
    {
        $this->classname = $classname;
    }

    /**
     * Sets the class' file destination
     * @param string $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * Sets the class' extend value
     * @param string $extend
     */
    public function setExtends($extends)
    {
        $this->extends = $extends;
    }

    /**
     * Sets the class' use modules
     * @param string $uses
     */
    public function setUses($uses)
    {
        $this->uses = $uses;
    }

    /**
     * Sets the class' contents
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * Sets the command that triggered the generation.
     * @param  StrataCommand $command
     */
    public function setCommandContext(StrataCommand $command)
    {
        $this->command = $command;
    }

    /**
     * Generates a file string content based on the global template.
     * @return string            The generated class string
     */
    protected function generateFileContents()
    {
        $data = $this->classTemplate;

        $data = str_replace("{EXTENDS}", $this->extends, $data);
        $data = str_replace("{NAMESPACE}", $this->namespace, $data);
        $data = str_replace("{CLASSNAME}", $this->classname, $data);
        $data = str_replace("{CONTENTS}", $this->contents, $data);
        $data = str_replace("{USES}", $this->uses, $data);

        return $data;
    }

    /**
     * Generates and writes a file in the file system
     * @param  boolean $last        Specifies if this is the last file in a queue
     */
    public function create($last = false)
    {
        if (!file_exists($this->destination)) {
            $dir = dirname($this->destination);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if (@file_put_contents($this->destination, $this->generateFileContents()) > 0) {
                $this->command->output->writeLn($this->command->tree($last) . $this->command->ok($this->destination));
            } else {
                $this->command->output->writeLn($this->command->tree($last) . $this->command->fail($this->destination));
            }

        } else {
            $this->command->output->writeLn($this->command->tree($last) . $this->command->skip($this->destination));
        }
    }
}
