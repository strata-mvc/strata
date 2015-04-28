<?php
/**
 */
namespace Strata\Shell;

use Strata\Utility\Inflector;
use Strata\Shell\StrataCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use \InvalidArgumentException;

/**
 * built-in Migration Shell
 */
class GenerateCommand extends StrataCommand
{
    protected $_classTemplate = "<?php
namespace {NAMESPACE};

class {CLASSNAME} extends {EXTENDS} {


}";


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
            )
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        $options = $input->getArgument('options');

        switch ($input->getArgument('type')) {
            case "controller" :
                $this->_renderController(Inflector::classify($options[0]). "Controller");
                break;

            case "model" :
                $this->_renderModel(Inflector::classify($options[0]). "Model");
                break;

            case "customposttype" :
                $this->_renderCustomPostType(Inflector::classify($options[0]). "Model", true);
                break;

            case "helper" :
                $this->_renderHelper(Inflector::classify($options[0]) . "Helper");
                break;

            case "taxonomy" :
                $this->_output->writeLn("Generating a taxonomy is not yet supported, but should be soon.");
                break;

            case "validator" :
                $this->_output->writeLn("Generating a validator is not yet supported, but should be soon.");
                break;

            case "route" :
                $this->_output->writeLn("Generating a route is not yet supported, but should be soon.");
                break;


            default : throw new InvalidArgumentException("That is not a valid command.");
        }

        $this->nl();

        $this->shutdown();
    }

    protected function _getNamespace()
    {
        return "App";
    }

    protected function _generateFileContents($namespace, $classname, $extends)
    {
        $data = $this->_classTemplate;
        $data = str_replace("{EXTENDS}", $extends, $data);
        $data = str_replace("{NAMESPACE}", $namespace, $data);
        $data = str_replace("{CLASSNAME}", $classname, $data);

        return $data;
    }




    protected function _renderController($classname)
    {
        $this->_output->writeLn("Scaffolding controller <info>$classname</info>");

        $namespace = $this->_getNamespace();
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "controller", "$classname.php"));

        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("$classname\Controller", $classname, "\{$namespace}\AppController");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->_output->writeLn($this->tree() . $this->ok($destination));
            } else {
                $this->_output->writeLn($this->tree() . $this->fail($destination));
            }
        } else {
            $this->_output->writeLn($this->tree() . $this->skip($destination));
        }

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "controller", $classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$namespace}\Test\Controller", "Test{$namespace}" , "\Strata\Test\Test");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->_output->writeLn($this->tree(true) . $this->ok($destination));
            } else {
                $this->_output->writeLn($this->tree(true) . $this->fail($destination));
            }
        } else {
            $this->_output->writeLn($this->tree(true) . $this->skip($destination));
        }
    }

    protected function _renderModel($classname, $isCustomPostType = false)
    {
        $this->_output->writeLn("Scaffolding model <info>$classname</info>");

        $namespace = $this->_getNamespace();
        $ctpExtend =  $isCustomPostType ? "\Strata\Model\CustomPostType\Entity" : "\{$namespace}\Model\AppModel";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "model", $classname . ".php"));

        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("$namespace\Model", $classname, $ctpExtend);
            if (@file_put_contents($destination, $contents) > 0) {
                $this->_output->writeLn($this->tree() . $this->ok($destination));
            } else {
                $this->_output->writeLn($this->tree() . $this->fail($destination));
            }
        } else {
            $this->_output->writeLn($this->tree() . $this->skip($destination));
        }

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "model", $classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$classname}\Test\Model", "Test{$classname}", "\Strata\Test\Test");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->_output->writeLn($this->tree(true) . $this->ok($destination));
            } else {
                $this->_output->writeLn($this->tree(true) . $this->fail($destination));
            }
        } else {
            $this->_output->writeLn($this->tree(true) . $this->skip($destination));
        }
    }

    protected function _renderHelper($classname)
    {
        $this->_output->writeLn("Scaffolding view helper <info>$classname</info>");
        $namespace = $this->_getNamespace();
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "view", "helper", $classname . ".php"));

        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$classname}\Test\View\Helper", $classname, "\{$namespace}\View\Helper\AppHelper");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->_output->writeLn($this->tree() . $this->ok($destination));
            } else {
                $this->_output->writeLn($this->tree() . $this->fail($destination));
            }
        } else {
            $this->_output->writeLn($this->tree() . $this->skip($destination));
        }

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "view", "helper", $classname . ".php"));
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents("{$namespace}\Tests\View\Helper", "Test{$classname}", "\Strata\Test\Test");
            if (@file_put_contents($destination, $contents) > 0) {
                $this->_output->writeLn($this->tree(true) . $this->ok($destination));
            } else {
                $this->_output->writeLn($this->tree(true) . $this->fail($destination));
            }
        } else {
            $this->_output->writeLn($this->tree(true) . $this->skip($destination));
        }
    }
}
