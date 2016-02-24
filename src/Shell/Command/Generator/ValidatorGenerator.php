<?php

namespace Strata\Shell\Command\Generator;

use Strata\Model\Validator\Validator;
use Strata\Strata;

/**
 * Generates a Strata model entity validator
 */
class ValidatorGenerator extends GeneratorBase
{
    /**
     * {@inheritdoc}
     */
    public function applyOptions(array $args)
    {
        $this->keyword = $args[0];
        $this->classname = Validator::generateClassName($this->keyword);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $this->command->output->writeLn("Scaffolding validator <info>{$this->classname}</info>");

        $this->generateValidator();
        $this->generateTest();
    }

    /**
     * Configures the class writer and makes it generate the
     * classes required by the object type.
     */
    protected function generateValidator()
    {
        $namespace = Strata::getNamespace() . "\\Model\\Validator";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Model", "Validator", "{$this->classname}.php"));

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Model\Validator\Validator as StrataValidator;\n");
        $writer->setExtends("StrataValidator");
        $writer->create();
    }

    /**
     * Configures the class writer and makes it generate the
     * test classes required by the object type.
     */
    protected function generateTest()
    {
        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Model", "Validator", $this->classname . "Test.php"));
        $namespace = Strata::getNamespace() . "\\Test\\Model\\Validator";

        $writer = $this->getWriter();
        $writer->setClassname($this->classname . "Test");
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Test\Test as StrataTest;\n");
        $writer->setExtends("StrataTest");
        $writer->create(true);
    }
}
