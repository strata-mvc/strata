<?php

namespace Strata\Shell\Command\Generator;

use Strata\Shell\Command\StrataCommandNamer;
use Strata\Strata;

/**
 * Generates a Strata shell command
 */
class CommandGenerator extends GeneratorBase
{
    /**
     * {@inheritdoc}
     */
    public function applyOptions(array $args)
    {
        $this->keyword = $args[0];
        $this->classname = StrataCommandNamer::generateClassName($this->keyword);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $this->command->output->writeLn("Scaffolding command <info>{$this->classname}</info>");

        $this->generateCommand();
        $this->generateTest();
    }

    /**
     * Configures the class writer and makes it generate the
     * classes required by the object type.
     */
    protected function generateCommand()
    {
        $namespace = Strata::getNamespace() . "\\Shell\\Command";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Shell", "Command", "{$this->classname}.php"));

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("
use Strata\Shell\Command\StrataCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
");
        $writer->setExtends("StrataCommand");
        $writer->setContents("
    protected function configure()
    {
        \$this
            ->setName('command_name')
            ->setDescription('Explain command behavior here.');
    }

    protected function execute(InputInterface \$input, OutputInterface \$output)
    {
        \$this->startup(\$input, \$output);

        // Enter command bahavior here.

        \$this->shutdown();
    }
");
        $writer->create();
    }

    /**
     * Configures the class writer and makes it generate the
     * test classes required by the object type.
     */
    protected function generateTest()
    {
        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Shell", "Command", $this->classname . "Test.php"));
        $namespace = Strata::getNamespace() . "\\Test\\Shell\\Command";

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Test\Test as StrataTest;\n");
        $writer->setExtends("StrataTest");
        $writer->create(true);
    }
}
