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
use InvalidArgumentException;

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
 *
 * @todo Route generation needs to be re-factored
 */
class GenerateCommand extends StrataCommand
{
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

        $type = strtolower($input->getArgument('type'));
        $options = (array)$input->getArgument('options');

        if (count($options)) {
            $generator = $this->getGenerator($type);
            $generator->applyOptions($options);
            $generator->generate();
        } else {
            throw new InvalidArgumentException("Missing required option arguments.");
        }

        $this->nl();
        $this->shutdown();
    }

    protected function getGenerator($type)
    {
        switch ($type) {
            case "controller":
                return new ControllerGenerator($this);

            case "model":
                return new ModelGenerator($this);

            case "customposttype":
                return new CustomPostTypeGenerator($this);

            case "viewhelper" :
            case "helper":
                return new HelperGenerator($this);

            case "taxonomy":
                return new TaxonomyGenerator($this);

            case "validator":
                return new ValidatorGenerator($this);

            case "command":
                return new CommandGenerator($this);

            // case "route":
            //     return new RouteGenerator($this);
        }

        throw new InvalidArgumentException("That is not a valid command.");
    }
}
