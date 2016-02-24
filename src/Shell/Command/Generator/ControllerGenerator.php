<?php
namespace Strata\Shell\Command\Generator;

use Strata\Controller\Controller;
use Strata\Strata;

/**
 * Generates a Strata controller
 */
class ControllerGenerator extends GeneratorBase
{
    /**
     * {@inheritdoc}
     */
    public function applyOptions(array $args)
    {
        $this->keyword = $args[0];
        $this->classname = Controller::generateClassName($this->keyword);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $this->command->output->writeLn("Scaffolding controller <info>{$this->classname}</info>");

        $this->generateController();
        $this->generateTest();
    }

    /**
     * Configures the class writer and makes it generate the
     * classes required by the object type.
     */
    protected function generateController()
    {
        $namespace = Strata::getNamespace() . "\\Controller";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Controller", "{$this->classname}.php"));

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setExtends("AppController");
        $writer->create();
    }

    /**
     * Configures the class writer and makes it generate the
     * test classes required by the object type.
     */
    protected function generateTest()
    {
        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Controller", $this->classname . "Test.php"));
        $namespace = Strata::getNamespace() . "\\Test\\Controller";

        $writer = $this->getWriter();
        $writer->setClassname($this->classname . "Test");
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Test\Test as StrataTest;\n");
        $writer->setExtends("StrataTest");
        $writer->create(true);
    }
}
