<?php
namespace Strata\Shell\Command\Generator;

class ControllerGenerator extends ClassWriter {

    public function generate()
    {
        $this->command->output->writeLn("Scaffolding controller <info>{$this->classname}</info>");

        $namespace = $this->_getNamespace();

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "Controller", "{$this->classname}.php"));
        $this->_createFile($destination, "$namespace\Controller", $this->classname, "AppController");

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "Controller", $this->classname . "Test.php"));
        $this->_createFile($destination, "$namespace\Test\Controller", "Test{$this->classname}" , "\Strata\Test\Test", true);
    }
}
