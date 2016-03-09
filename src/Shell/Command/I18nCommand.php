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

/**
 * Automates Strata's localization actions.
 *
 * Intended use include:
 *     <code>./strata i18n extract</code>
 */
class I18nCommand extends StrataCommandBase
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

    /**
     * Gets the list of application Locales
     * @return array A list of Locale objects
     */
    protected function getLocales()
    {
        return Strata::app()->i18n->getLocales();
    }

    /**
     * Specifies whether the project is actively using
     * localization.
     * @return boolean
     */
    protected function projectHasActiveLocales()
    {
        return Strata::app()->i18n->hasActiveLocales();
    }

    /**
     * Confirms the directories required to build the mo and po files
     * exist.
     */
    private function ensureFolderStructure()
    {
        $localeDir = Strata::getLocalePath();
        if (!is_dir($localeDir)) {
            mkdir($localeDir);
        }
    }

    /**
     * Saves extracted strings from the project to
     * locale mo and po files.
     */
    private function saveStringToLocales()
    {
        $gettextEntries = $this->extractGettextStrings();
        foreach ($this->getLocales() as $locale) {
            $this->addGettextEntriesToLocale($locale, $gettextEntries);
        }
    }

    /**
     * Saves the list of $translation to the $locale.
     * @param  Locale       $locale
     * @param  Translations $translation
     */
    private function addGettextEntriesToLocale(Locale $locale, Translations $translations)
    {
        // The locally modified locales are stored in a
        // file that is identified as belonging to the current
        // environment.
        $envPoFile = $locale->getPoFilePath(WP_ENV);
        $envTranslation = $locale->hasPoFile(WP_ENV) ?
            Translations::fromPoFile($envPoFile) :
            new Translations();

        $i18n = Strata::app()->i18n;
        $i18n->hardTranslationSetMerge($locale, $envTranslation, $translations);
        $i18n->generateTranslationFiles($locale);
    }

    /**
     * Extracts gettext string from predefined areas within the project.
     * @return Translation
     */
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

            // Merge all translation objects into a bigger one
            foreach ($translationObjects as $t) {
                if (is_null($translation)) {
                    $translation = $t;
                } else {
                    $translation->mergeWith($t);
                }
            }
        }

        return $translation;
    }

    /**
     * Recurses through a directory looking for files matching the $lookingFor pattern.
     * Returns the extracted string from these matches.
     * @param  string $baseDir
     * @param  string $lookingFor
     * @return array
     */
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

    /**
     * Extracts gettext string from $filename
     * @param  string $filename
     * @return array
     */
    private function extractFrom($filename)
    {
        return WpCode::fromFile($filename);
    }
}
