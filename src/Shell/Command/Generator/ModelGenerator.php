<?php
namespace Strata\Shell\Command\Generator;

use Strata\Model\Model;
use Strata\Model\CustomPostType\ModelEntity;
use Strata\Strata;

class ModelGenerator extends GeneratorBase
{
    public function applyOptions(array $args)
    {
        $this->keyword = $args[0];
        $this->classname = Model::generateClassName($this->keyword);
    }

    public function generate()
    {
        $this->command->output->writeLn($this->getScaffoldMessage());

        $this->generateModel();
        $this->generateEntity();
        $this->generateTest();
        $this->generateEntityTest();
    }

    protected function getExtends()
    {
        return "AppModel";
    }

    protected function getScaffoldMessage()
    {
        return "Scaffolding model <info>{$this->classname}</info>";
    }

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

    protected function generateEntity()
    {
        $classname = ModelEntity::generateClassName($this->keyword);
        $namespace = Strata::getNamespace() . "\\Model\\Entity";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Model", "Entity",  $classname . ".php"));

        $writer = $this->getWriter();
        $writer->setClassname($classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Model\CustomPostType\ModelEntity as StrataModelEntity;\n");
        $writer->setExtends("StrataModelEntity");
        $writer->create();
    }

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
