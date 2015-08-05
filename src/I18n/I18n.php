<?php
namespace Strata\I18n;

use Strata\Strata;
use Strata\Utility\Hash;
use Strata\I18n\Locale;
use Strata\Controller\Request;

use Gettext\Translations;
use Gettext\Translation;

/**
 * Handles  localization
 *
 * @package       Strata.i18n
 */
class i18n {

    /** The default text domain used buy the class */
    const DOMAIN = "strata_i18n";

    /** @var array The list of instanciated locales in the application */
    protected $locales = array();

    /** @var Locale The locale that is currently active. */
    protected $currentLocale = null;

    /**
     * The class initializer is meant to be called only once during the
     * Strata kickoff.
     */
    public function initialize()
    {
        $this->resetLocaleCache();
        if ($this->shouldAddWordpressHooks()) {
            $this->registerHooks();
        }
    }

    /**
     * Sets the locale based on the current process' context.
     * @return string|null The locale code
     */
    public function setAndApplyCurrentLanguageByContext()
    {
        $this->setCurrentLocaleByContext();
        $locale = $this->getCurrentLocale();

        if (!is_null($locale)) {
            return $locale->getCode();
        }
    }

    /**
     * Assigns the theme textdomain to Wordpress using 'load_theme_textdomain'.
     * @return boolean The result of load_theme_textdomain
     */
    public function applyLocale()
    {
        return load_theme_textdomain("strata_i18n", Strata::getLocalePath());
    }

    /**
     * Clears the current locale cache and rebuilds it based
     * on the loaded configuration.
     */
    public function resetLocaleCache()
    {
        $this->locales = array();

        if ($this->isLocalized()) {
            $this->locales = $this->parseLocalesFromConfig();
        }
    }

    /**
     * Sets the current locale based on either a GET parameter or the locale URL prefix.
     * If none is found, it will return the default locale.
     */
    public function setCurrentLocaleByContext()
    {
        $request = new Request();
        if ($request->hasGet("locale")) {
            $locale = $this->getLocaleByCode($request->get("locale"));
            if (!is_null($locale)) {
                $this->setLocale($locale);
                return;
            }
        }

        if (preg_match('/\/('.implode('|', $this->getLocaleUrls()).')\//i', $_SERVER['REQUEST_URI'], $match)) {
            $locale = $this->getLocaleByUrl($match[1]);
            if (!is_null($locale)) {
                $this->setLocale($locale);
            }
            return;
        }

        if ($this->hasDefaultLocale()) {
            $this->setLocale($this->getDefaultLocale());
        }
    }

    /**
     * Specifies whether localization settings are present in Strata's configuration array.
     * @return boolean
     */
    public function isLocalized()
    {
        return !is_null(Strata::config("i18n.locales"));
    }

    /**
     * Specifies if the list of active locales
     * has elements
     * @return boolean
     */
    public function hasActiveLocales()
    {
        return count($this->locales) > 0;
    }

    /**
     * Returns the list of Locale objects
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Returns a locale object by code
     * @param  string $code The same code used when declaring the locale in the configuration value
     * @return Locale|null
     */
    public function getLocaleByCode($code)
    {
        if (array_key_exists($code, $this->locales)) {
            return $this->locales[$code];
        }
    }
    /**
     * Returns a locale object by locale url key
     * @param  string $url The locale url as set when configuring (defaults to the locale code).
     * @return Locale|null
     */
    public function getLocaleByUrl($url)
    {
        foreach ($this->getLocales() as $locale) {
            if ($locale->getUrl() === $url) {
                return $locale;
            }
        }
    }

    /**
     * Returns the locale that is currently used.
     * @return Locale
     */
    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the code of the locale that is currently used.
     * @return string|null
     */
    public function getCurrentLocaleCode()
    {
        $locale = $this->getCurrentLocale();
        if (!is_null($locale)) {
            return $locale->getCode();
        }
    }

    /**
     * Specifies whether the application has a default locale defined.
     * @return boolean
     */
    public function hasDefaultLocale()
    {
        return !is_null($this->getDefaultLocale());
    }

    /**
     * Returns the default locale based off the localization documentation
     * specified in the Strata configuration file.
     * @return Locale
     */
    public function getDefaultLocale()
    {
        $locales = $this->getLocales();

        foreach ($locales as $locale) {
            if ($locale->isDefault()) {
                return $locale;
            }
        }

        return array_pop($locales);
    }

    /**
     * Loads the translations from the locale's PO file and returns the list.
     * @param  string $localeCode
     * @return array
     */
    public function getTranslations($localeCode)
    {
        $locale = $this->getLocaleByCode($localeCode);

        if (!$locale->hasPoFile()) {
            throw new Exception("$localeCode is not a supported locale.");
        }

        return Translations::fromPoFile($locale->getPoFilePath());
    }

    public function saveTranslations(Locale $locale, array $postedTranslations)
    {
        $poFile = $locale->getPoFilePath();
        $originalTranslations = Translations::fromPoFile($poFile);
        $newTranslations = new Translations();

        foreach ($postedTranslations as $t) {
            $original = html_entity_decode($t['original']);
            $context = html_entity_decode($t['context']);

            $translation = $originalTranslations->find($context, $original);
            if ($translation === false) {
                $translation = new Translation($context, $original, $t['plural']);
            }

            $translation->setTranslation($t['translation']);
            $translation->setPluralTranslation($t['pluralTranslation']);
            $newTranslations[] = $translation;
        }

        $originalTranslations->mergeWith($newTranslations, Translations::MERGE_HEADERS | Translations::MERGE_COMMENTS | Translations::MERGE_ADD | Translations::MERGE_LANGUAGE);
        $originalTranslations->toPoFile($poFile);
    }

    /**
     * Compares two locales to see if $locale is the one currently
     * active.
     * @param  Locale  $locale
     * @return boolean
     */
    public function isCurrentlyActive(Locale $locale)
    {
        $current = $this->getCurrentLocale();
        return $locale->getCode() === $current->getCode();
    }

    /**
     * Registers Wordpress hooks required by the class.
     */
    protected function registerHooks()
    {
        add_action('after_setup_theme', array($this, "applyLocale"));
        add_filter('locale', array($this, "setAndApplyCurrentLanguageByContext"), 999);
    }

    /**
     * Specifies whether the class should be registering the Wordpress hooks.
     * Based on Wordpress existence (to account for Shell) and locale presence.
     * @return boolean
     */
    private function shouldAddWordpressHooks()
    {
        return function_exists('add_action') && $this->isLocalized();
    }

    /**
     * Goes through the list of localization configurations values in Strata's
     * configuration file.
     * @return array A list of instanciated Locale object.
     */
    protected function parseLocalesFromConfig()
    {
        $localeInfos = Hash::normalize(Strata::config("i18n.locales"));
        $locales = array();

        foreach ($localeInfos as $key => $config) {
            $locales[$key] = new Locale($key, $config);
        }

        return $locales;
    }

    /**
     * Sets the active locale
     * @param Locale $locale
     */
    protected function setLocale(Locale $locale)
    {
        $this->currentLocale = $locale;
    }

    /**
     * Returns the list of urls identifiers of all the
     * active locales
     * @return array
     */
    private function getLocaleUrls()
    {
        $urls = array();
        foreach ($this->getLocales() as $locale) {
            $urls[] = $locale->getUrl();
        }
        return $urls;
    }

}
