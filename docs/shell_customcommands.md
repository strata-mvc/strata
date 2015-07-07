---
layout: docs
title: Custom commands
permalink: /docs/shell/customcommands/
---

Projects often have a need for customized command line accessible scripts, may it be to be called from a cron or manually from the command line.


## Generating a custom command

Using the command line, run the `generate` command from your project's base directory. Here we create a command called `bundle`.

~~~ sh
$  bin/strata generate command bundle
~~~

The command will create a new class in the `src/Shell` directory.

~~~ sh
Scaffolding command BundleCommand
  ├── [ OK ] src/Shell/Command/BundleCommand.php
  └── [ OK ] test/Shell/Command/BundleCommandTest.php
~~~

## Executing

Custom commands are being auto-loaded when you call the script. Therefore, the previous `bundle` script can be called using:

~~~ sh
$  bin/strata bundle
~~~

## Requirements

StrataCommand inherit from Symfony's Console component. You will have to use their classes to manipulate input and output data.

The class needs to have a `configure()` function that declares the command name and description as well as potential parameters. It also requires a function called `execute()` that will run when the command is called.

More information can be found in [Symfony's documentation](http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command).

## Example

The following is an example `bundle` command. It goes through all the themes installed on the current project and executes installation scripts of known frontend libraries.

Looking at this class should give you a good understanding of what is possible with custom commands.

~~~ php
<?php
namespace App\Shell\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The bundle command will ensure the application is correctly configured
 * when the project is first setup.
 */
class BundleCommand extends \Strata\Shell\Command\StrataCommand {

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('bundle')
            ->setDescription('Bundles the project frontend files');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);
        $this->bundleThemesFrontend();
        $this->shutdown();
    }

    private function bundleThemesFrontend()
    {
        foreach ($this->getThemesDirectories() as $themePath) {
            $this->bundleFrontend($themePath);
        }
    }

    private function getThemesDirectories()
    {
        return glob("web/app/themes/*/");
    }

    /**
     * Goes in the directory and bundles the frontend tools
     * @param  string $themePath
     */
    private function bundleFrontend($themePath)
    {
        $this->output->writeln("Creating frontend bundle for <info>$themePath</info>");
        exec("cd $themePath && npm install && bower install && grunt dist");
        $this->nl();
    }
}
?>
~~~
