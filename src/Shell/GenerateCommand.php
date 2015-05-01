<?php
/**
 */
namespace Strata\Shell;

use Strata\Utility\Hash;
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
                $this->_renderController($classname."Controller");
                break;

            case "model" :
                $this->_renderModel($classname);
                break;

            case "customposttype" :
                $this->_renderModel($classname, true);
                break;

            case "helper" :
                $this->_renderHelper($classname."Helper");
                break;

            case "taxonomy" :
                $this->_renderTaxonomy($classname);
                break;

            case "validator" :
                $this->_renderValidator($classname."Validator");
                break;

            case "route" :
                if (count($options) < 3) {
                    throw new InvalidArgumentException("A route requires 3 parameters: The request type, the url to match and the MVC destination.");
                }
                $this->_addRoute($options[0], $options[1], $options[2]);
                break;

            default : throw new InvalidArgumentException("That is not a valid command.");
        }

        $this->nl();

        $this->shutdown();
    }

    /**
     * Returns the namespace of the current project.
     * @return string A valid namespace string.
     */
    protected function _getNamespace()
    {
        return "App";
    }

    /**
     * Generates a file string content based on the global template.
     * @param  string $namespace The class' namespace
     * @param  string $classname The class name
     * @param  string $extends   The extending class
     * @return string            The generated class string
     */
    protected function _generateFileContents($namespace, $classname, $extends)
    {
        $data = $this->_classTemplate;

        $data = str_replace("{EXTENDS}", $extends, $data);
        $data = str_replace("{NAMESPACE}", $namespace, $data);
        $data = str_replace("{CLASSNAME}", $classname, $data);

        return $data;
    }

    /**
     * Generates and writes a file in the file system
     * @param  string  $destination The file's destination
     * @param  string $namespace The class' namespace
     * @param  string $classname The class name
     * @param  string $extends   The extending class
     * @param  boolean $last     Specifies if this is the last file in a queue
     * @return null
     */
    protected function _createFile($destination, $namespace, $classname, $extends, $last = false)
    {
        if (!file_exists($destination)) {
            $contents = $this->_generateFileContents($namespace, $classname, $extends);
            if (@file_put_contents($destination, $contents) > 0) {
                $this->_output->writeLn($this->tree($last) . $this->ok($destination));
            } else {
                $this->_output->writeLn($this->tree($last) . $this->fail($destination));
            }
        } else {
            $this->_output->writeLn($this->tree($last) . $this->skip($destination));
        }
    }

    /**
     * Creates a Controller class file
     * @param  string $classname The class name
     * @return null
     */
    protected function _renderController($classname)
    {
        $this->_output->writeLn("Scaffolding controller <info>$classname</info>");

        $namespace = $this->_getNamespace();

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "controller", "$classname.php"));
        $this->_createFile($destination, "$namespace\Controller", $classname, "\{$namespace}\AppController");

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "controller", $classname . "Test.php"));
        $this->_createFile($destination, "{$namespace}\Test\Controller", "Test{$namespace}" , "\Strata\Test\Test", true);
    }

    /**
     * Creates a Model class file
     * @param  string $classname The class name
     * @return null
     */
    protected function _renderModel($classname, $isCustomPostType = false)
    {
        $this->_output->writeLn("Scaffolding model <info>$classname</info>");

        $namespace = $this->_getNamespace();

        $ctpExtend =  $isCustomPostType ? "\Strata\Model\CustomPostType\Entity" : "\{$namespace}\Model\AppModel";
        $destination = implode(DIRECTORY_SEPARATOR, array("src", "model", $classname . ".php"));
        $this->_createFile($destination, "$namespace\Model", $classname, $ctpExtend);

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "model", $classname . "Test.php"));
        $this->_createFile($destination, "{$namespace}\Test\Model", "Test{$classname}", "\Strata\Test\Test", true);
    }

    /**
     * Creates a Taxonomy Model class file
     * @param  string $classname The class name
     * @return null
     */
    protected function _renderTaxonomy($classname)
    {
        $this->_output->writeLn("Scaffolding taxonomy <info>$classname</info>");

        $namespace = $this->_getNamespace();

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "model", $classname . ".php"));
        $this->_createFile($destination, "$namespace\Model", $classname, "\Strata\Model\CustomPostType\TaxonomyEntity");

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "model", $classname . "Test.php"));
        $this->_createFile($destination, "{$namespace}\Test\Model", "Test{$classname}", "\Strata\Test\Test", true);
    }

    /**
     * Creates a Validator class file
     * @param  string $classname The class name
     * @return null
     */
    protected function _renderValidator($classname)
    {
        $this->_output->writeLn("Scaffolding validator <info>$classname</info>");

        $namespace = $this->_getNamespace();

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "model", "validator", $classname . ".php"));
        $this->_createFile($destination, "$namespace\Model\Validator", $classname, "\Strata\Model\Validator");

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "model", "validator", $classname . "Test.php"));
        $this->_createFile($destination, "{$namespace}\Test\Model", "Test{$classname}", "\Strata\Test\Test", true);
    }

    /**
     * Creates a View helper class file
     * @param  string $classname The class name
     * @return null
     */
    protected function _renderHelper($classname)
    {
        $this->_output->writeLn("Scaffolding view helper <info>$classname</info>");

        $namespace = $this->_getNamespace();

        $destination = implode(DIRECTORY_SEPARATOR, array("src", "view", "helper", $classname . ".php"));
        $this->_createFile($destination, "{$namespace}\View\Helper", $classname, "\{$namespace}\View\Helper\AppHelper");

        $destination = implode(DIRECTORY_SEPARATOR, array("test", "view", "helper", $classname . "Test.php"));
        $this->_createFile($destination, "{$namespace}\Tests\View\Helper", "Test{$classname}", "\Strata\Test\Test", true);
    }

    /**
     * Adds a new route entry to the strata.php configuration file.string
     * @param string $type        The type of request to handle
     * @param string $url         The matching url
     * @param string $destination A valid MVC destination
     */
    protected function _addRoute($type, $url, $destination)
    {
        $this->_output->writeLn("Add new route to <info>$destination</info>");

        $target = explode("#", $destination);
        $className = \Strata\Strata::getNamespace() . "\\Controller\\" . $target[0];

        if(class_exists($className)) {
            $this->_output->writeLn($this->tree(true) . $this->fail('No file matched the controller handled by this route. Looked for ' . $this->info($className) . '.'));
            return;
        }

        if (count($target) > 1 && !method_exists($className, $target[1])) {
            $this->_output->writeLn($this->tree(true) . $this->fail('No method named '. $target[1] .' is declared by '. $className .'.'));
            return;
        }

        $currentConfig = \Strata\Strata::parseProjectConfigFile();
        $newConfig = Hash::insert($currentConfig, "routes", array(strtoupper($type), $url, $destination));

        $this->_output->writeLn($this->tree(true) . $this->ok('Added route : $type | $url -> $destination.'));

        return \Strata\Strata::writeProjectConfigFile($newConfig);
    }

}
