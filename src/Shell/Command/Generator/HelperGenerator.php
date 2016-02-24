<?php

namespace Strata\Shell\Command\Generator;

use Strata\View\Helper\Helper;
use Strata\Strata;

/**
 * Generates a Strata view helper
 */
class HelperGenerator extends GeneratorBase
{
    /**
     * {@inheritdoc}
     */
    public function applyOptions(array $args)
    {
        $this->keyword = $args[0];
        $this->classname = Helper::generateClassName($this->keyword);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $this->command->output->writeLn("Scaffolding view helper <info>{$this->classname}</info>");

        $this->generateHelper();
        $this->generateTest();
    }

    /**
     * Configures the class writer and makes it generate the
     * classes required by the object type.
     */
    protected function generateHelper()
    {
        $namespace = Strata::getNamespace() . "\\View\\Helper";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "View", "Helper", "{$this->classname}.php"));

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setExtends("AppHelper");
        $writer->create();
    }

    /**
     * Configures the class writer and makes it generate the
     * test classes required by the object type.
     */
    protected function generateTest()
    {
        $destination = implode(DIRECTORY_SEPARATOR, array("test", "View", "Helper", $this->classname . "Test.php"));
        $namespace = Strata::getNamespace() . "\\Test\\View\\Helper";

        $writer = $this->getWriter();
        $writer->setClassname($this->classname . "Test");
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Test\Test as StrataTest;\n");
        $writer->setExtends("StrataTest");
        $writer->create(true);
    }
}
