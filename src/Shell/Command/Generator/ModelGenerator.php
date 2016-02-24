<?php

namespace Strata\Shell\Command\Generator;

use Strata\Model\Model;
use Strata\Model\CustomPostType\ModelEntity;
use Strata\Strata;

/**
 * Generates a Strata model
 */
class ModelGenerator extends GeneratorBase
{
    /**
     * {@inheritdoc}
     */
    public function applyOptions(array $args)
    {
        $this->keyword = $args[0];
        $this->classname = Model::generateClassName($this->keyword);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $this->command->output->writeLn($this->getScaffoldMessage());

        $this->generateModel();
        $this->generateEntity();
        $this->generateTest();
        $this->generateEntityTest();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtends()
    {
        return "AppModel";
    }

    /**
     * {@inheritdoc}
     */
    protected function getScaffoldMessage()
    {
        return "Scaffolding model <info>{$this->classname}</info>";
    }

    /**
     * Configures the class writer and makes it generate the
     * classes required by the object type.
     */
    protected function generateModel()
    {
        $namespace = Strata::getNamespace() . "\\Model";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Model", $this->classname . ".php"));

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setExtends($this->getExtends());
        $writer->create();
    }

    /**
     * Configures the class writer and makes it generate the
     * test classes required by the object type.
     */
    protected function generateTest()
    {
        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Model", $this->classname . "Test.php"));
        $namespace = Strata::getNamespace() . "\\Test\\Model";

        $writer = $this->getWriter();
        $writer->setClassname($this->classname . "Test");
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Test\Test as StrataTest;\n");
        $writer->setExtends("StrataTest");
        $writer->create();
    }

    /**
     * Configures the class writer and makes it generate the
     * accompanying model entity required by the object type.
     */
    protected function generateEntity()
    {
        $classname = ModelEntity::generateClassName($this->keyword);
        $namespace = Strata::getNamespace() . "\\Model\\Entity";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Model", "Entity",  $classname . ".php"));

        $writer = $this->getWriter();
        $writer->setClassname($classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setExtends("AppModelEntity");
        $writer->create();
    }

    /**
     * Configures the class writer and makes it generate the
     * test classes required by the accompanying model entity.
     */
    protected function generateEntityTest()
    {
        $classname = ModelEntity::generateClassName($this->keyword);
        $namespace = Strata::getNamespace() . "\\Test\\Model\\Entity";
        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Model", "Entity",  $classname . "Test.php"));

        $writer = $this->getWriter();
        $writer->setClassname($classname . "Test");
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Test\Test as StrataTest;\n");
        $writer->setExtends("StrataTest");
        $writer->create(true);
    }
}
