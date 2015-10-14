<?php
namespace Strata\Shell\Command\Generator;

class TaxonomyGenerator extends ClassWriter
{

    /**
     * Creates a Taxonomy Model class file
     * @return null
     */
    public function generate()
    {
        $this->command->output->writeLn("Scaffolding taxonomy <info>{$this->classname}</info>");

        $namespace = $this->_getNamespace();

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Model", $this->classname . ".php"));
        $this->_createFile($destination, "$namespace\Model", $this->classname, "\Strata\Model\Taxonomy\Taxonomy");

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Model", $this->classname . "Test.php"));
        $this->_createFile($destination, "$namespace\Test\Model", "Test{$this->classname}", "\Strata\Test\Test", true);
    }
}
