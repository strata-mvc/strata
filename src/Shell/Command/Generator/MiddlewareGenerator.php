<?php

namespace Strata\Shell\Command\Generator;

use Strata\Shell\Command\StrataCommandNamer;
use Strata\Strata;

/**
 * Generates a Strata middleware
 */
class MiddlewareGenerator extends GeneratorBase
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
        $this->command->output->writeLn("Scaffolding middleware <info>{$this->classname}</info>");

        $this->generateMiddleware();
        $this->generateTest();
    }

    /**
     * Configures the class writer and makes it generate the
     * classes required by the object type.
     */
    protected function generateMiddleware()
    {
        $namespace = Strata::getNamespace() . "\\Middleware";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Middleware", "{$this->classname}.php"));

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("
use Strata\Middleware\Middleware as StrataMiddleware;
");
        $writer->setExtends("StrataMiddleware");
        $writer->setContents("
    public function initialize()
    {

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
        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Middleware", $this->classname . "Test.php"));
        $namespace = Strata::getNamespace() . "\\Test\\Middleware";

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Test\Test as StrataTest;\n");
        $writer->setExtends("StrataTest");
        $writer->create(true);
    }
}
