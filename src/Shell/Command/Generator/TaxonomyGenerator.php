<?php

namespace Strata\Shell\Command\Generator;

use Strata\Model\Taxonomy\Taxonomy;
use Strata\Strata;

/**
 * Generates a Strata taxonomy class
 */
class TaxonomyGenerator extends GeneratorBase
{
    /**
     * {@inheritdoc}
     */
    public function applyOptions(array $args)
    {
        $this->keyword = $args[0];
        $this->classname = Taxonomy::generateClassName($this->keyword);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $this->command->output->writeLn("Scaffolding taxonomy <info>{$this->classname}</info>");

        $this->generateTaxonomy();
        $this->generateTest();
    }

    /**
     * Configures the class writer and makes it generate the
     * classes required by the object type.
     */
    protected function generateTaxonomy()
    {
        $namespace = Strata::getNamespace() . "\\Model\\Taxonomy";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Model", "Taxonomy", "{$this->classname}.php"));

        $writer = $this->getWriter();
        $writer->setClassname($this->classname);
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Model\Taxonomy\Taxonomy as StrataTaxonomy;\n");
        $writer->setExtends("StrataTaxonomy");
        $writer->create();
    }

    /**
     * Configures the class writer and makes it generate the
     * test classes required by the object type.
     */
    protected function generateTest()
    {
        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Model", "Taxonomy", $this->classname . "Test.php"));
        $namespace = Strata::getNamespace() . "\\Test\\Model\\Taxonomy";

        $writer = $this->getWriter();
        $writer->setClassname($this->classname . "Test");
        $writer->setNamespace($namespace);
        $writer->setDestination($destination);
        $writer->setUses("\nuse Strata\Test\Test as StrataTest;\n");
        $writer->setExtends("StrataTest");
        $writer->create(true);
    }
}
