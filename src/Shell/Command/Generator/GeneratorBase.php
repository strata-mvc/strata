<?php
/**
 */
namespace Strata\Shell\Command\Generator;

use Strata\Strata;
use Strata\Shell\Command\StrataCommand;

/**
 * Automates repetitive creation of code files. It validates the class names and
 * file locations based on the set of guidelines promoted by Strata.
 *
 * Intended use include:
 *     <code>./strata generate controller User</code>
 *     <code>./strata generate customposttype Fruit</code>
 *     ...
 */
abstract class GeneratorBase
{
    /**
     * @var StrataCommand A reference to the current command interface
     */
    protected $command = null;
    protected $keyword;
    protected $classname;

    /**
     * @var ClassWriter A reference to the current class writer
     */
    protected $writer = null;

    function __construct(StrataCommand $command)
    {
        $this->command = $command;
    }

    /**
     * Send option values to the generator so it can manipulate
     * the information.
     * @param  array  $args
     */
    abstract public function applyOptions(array $args);

    /**
     * Performs all the operations required by the generator.
     */
    abstract public function generate();

    /**
     * Obtain an instantiated ClassWriter object to create files.
     * @return ClassWriter
     */
    protected function getWriter()
    {
        $writer = new ClassWriter();
        $writer->setCommandContext($this->command);
        return $writer;
    }
}
