<?php
/**
 */
namespace Strata\Shell\Command;

use Strata\Utility\Inflector;
use Strata\Shell\Command\StrataCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use \InvalidArgumentException;

use Strata\Shell\Command\Generator\ControllerGenerator;
use Strata\Shell\Command\Generator\ModelGenerator;
use Strata\Shell\Command\Generator\FormGenerator;
use Strata\Shell\Command\Generator\CustomPostTypeGenerator;
use Strata\Shell\Command\Generator\HelperGenerator;
use Strata\Shell\Command\Generator\TaxonomyGenerator;
use Strata\Shell\Command\Generator\ValidatorGenerator;
use Strata\Shell\Command\Generator\CommandGenerator;
use Strata\Shell\Command\Generator\RouteGenerator;


/**
 * Automates repetitive creation of code files. It validates the class names and
 * file locations based on the set of guidelines promoted by Strata.
 *
 * Intended use include:
 *     <code>bin/strata generate controller User</code>
 *     <code>bin/strata generate customposttype Task</code>
 *     ...
 */
class GenerateCommand extends StrataCommand
{
    /**
     * The base string template for creating empty class files.
     *
     * @var string
     */
    protected $_classTemplate = "<?php
namespace {NAMESPACE};

class {CLASSNAME} extends {EXTENDS} {


}";

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generates something for you.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'The type of object you wish to generate.'
            )
            ->addArgument(
                'options',
                InputArgument::IS_ARRAY,
                'The options to the command you wish to run.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        $options = $input->getArgument('options');
        $classname = Inflector::classify($options[0]);

        switch ($input->getArgument('type')) {
        case "controller" :
            $generator = new ControllerGenerator($this);
            $generator->setClassName($classname."Controller");
            break;

        case "model" :
            $generator = new ModelGenerator($this);
            $generator->setClassName($classname);
            break;

        case "form" :
            $generator = new FormGenerator($this);
            $generator->setClassName($classname."Form");
            break;

        case "customposttype" :
            $generator = new CustomPostTypeGenerator($this);
            $generator->setClassName($classname);
            break;

        case "helper" :
            $generator = new HelperGenerator($this);
            $generator->setClassName($classname."Helper");
            break;

        case "taxonomy" :
            $generator = new TaxonomyGenerator($this);
            $generator->setClassName($classname);
            break;

        case "validator" :
            $generator = new ValidatorGenerator($this);
            $generator->setClassName($classname."Validator");
            break;

        case "command" :
            $generator = new CommandGenerator($this);
            $generator->setClassName($classname."Command");
            break;

        case "route" :
            $generator = new RouteGenerator($this);
            $generator->configure($options);
            break;

        default : 
            throw new InvalidArgumentException("That is not a valid command.");
        }

        $generator->generate();

        $this->nl();
        $this->shutdown();
    }
}
