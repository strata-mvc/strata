<?php
namespace Strata\Shell\Command\Generator;

class ValidatorGenerator extends ClassWriter {

    /**
     * Creates a Validator class file
     * @return null
     */
    public function generate()
    {
        $this->command->output->writeLn("Scaffolding validator <info>{$this->classname}</info>");

        $namespace = $this->_getNamespace();

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Model", "Validator", $this->classname . ".php"));
        $this->_createFile($destination, "$namespace\Model\Validator", $this->classname, "\Strata\Model\Validator\Validator");

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Model", "Validator", $this->classname . "Test.php"));
        $this->_createFile($destination, "$namespace\Test\Model", "Test{$this->classname}", "\Strata\Test\Test", true);
    }
}
