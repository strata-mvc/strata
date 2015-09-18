<?php
namespace Strata\Shell\Command;

use Strata\Strata;
use Strata\Utility\Hash;
use Strata\I18n\i18n;
use Strata\I18n\WpCode;
use Strata\I18n\Locale;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

use Gettext\Translations;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class I18nCommand extends StrataCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('i18n')
            ->setDescription('Translates the current application\'s source code and themes.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'One of the following: extract.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startup($input, $output);

        if ($this->projectHasActiveLocales()) {
            $this->ensureFolderStructure();
            $this->saveStringToLocales();

        } else {
            $this->output->writeln("This project has no configured locale.");
        }

        $this->nl();

        $this->shutdown();
    }

    protected function getLocales()
    {
        return Strata::app()->i18n->getLocales();
    }

    protected function projectHasActiveLocales()
    {
        return Strata::app()->i18n->hasActiveLocales();
    }

    private function ensureFolderStructure()
    {
        $localeDir = Strata::getLocalePath();
        if (!is_dir($localeDir)) {
            mkdir($localeDir);
        }
    }

    private function saveStringToLocales()
    {
        $tanslation = $this->extractGettextStrings();

        foreach ($this->getLocales() as $locale) {
            $this->saveStringToLocale($locale, $tanslation);
        }
    }

    private function saveStringToLocale(Locale $locale, Translations $translation)
    {
        $poFilename = $locale->getPoFilePath();

        // Merge with an existing .po
        if ($locale->hasPoFile()) {
            $poTranslations = Translations::fromPoFile($poFilename);
            $translation->mergeWith($poTranslations);
        }

        $app = Strata::app();
        $configValue = $app->getConfig("i18n.textdomain");
        $textDomain = is_null($configValue) ? I18n::DOMAIN : $configValue;

        $translation->setDomain($textDomain);
        $translation->setHeader('Text Domain', $textDomain);

        $translation->toPoFile($poFilename);
        $translation->toMoFile($locale->getMoFilePath());
    }

    private function extractGettextStrings()
    {
        $translation = null;
        $translationObjects = array();
        $lookupDirectories = array(
            Strata::getVendorPath() . 'francoisfaubert' . DIRECTORY_SEPARATOR . 'strata' . DIRECTORY_SEPARATOR . 'src',
            Strata::getSrcPath(),
            Strata::getThemesPath(),
        );

        foreach ($lookupDirectories as $directory) {
            $translationObjects = $this->recurseThroughDirectory($directory);
        }

        // Merge all translation objects into a bigger one
        foreach ($translationObjects as $t) {
            if (is_null($translation)) {
                $translation = $t;
            } else {
                $translation->mergeWith($t);
            }
        }

        return $translation;
    }

    private function recurseThroughDirectory($baseDir, $lookingFor = "/(.*)\.php$/i")
    {
        $results = array();
        $di = new RecursiveDirectoryIterator($baseDir);

        $this->output->writeLn("Scanning $baseDir...");

        foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
            if (preg_match($lookingFor, $filename)) {
                $results[] = $this->extractFrom($filename);
            }
        }

        return $results;
    }

    private function extractFrom($filename)
    {
        return WpCode::fromFile($filename);
    }

}
