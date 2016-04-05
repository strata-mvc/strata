<?php

namespace Strata\I18n;

use Strata\Strata;
use Strata\Utility\Hash;
use Strata\I18n\Locale;
use Strata\Controller\Request;
use Strata\Router\Router;
use Gettext\Translations;
use Gettext\Translation;
use Exception;

/**
 * Handles code localization using Gettext for PHP.
 * This requires a PHP installation that is compiled with extension=php_gettext.dll
 */
class i18n
{
    /**
     * @var string The default text domain used buy the class
     */
    const DOMAIN = "strata_i18n";

    /**
     * @var array The list of instantiated locales in the application
     */
    protected $locales = array();

    /**
     * Locale @var Locale The locale that is currently active.
     */
    protected $currentLocale = null;

    /**
     * The class initializer is meant to be called only once during the
     * Strata kickoff.
     */
    public function initialize()
    {
        $this->startSession();
        $this->resetLocaleCache();
        $this->setCurrentLocaleByContext();

        if ($this->shouldAddWordpressHooks()) {
            $this->registerHooks();
        }
    }

    /**
     * Sets the locale based on the current process' context.
     * @return string The locale code
     */
    public function applyCurrentLanguageByContext()
    {
        $locale = $this->getCurrentLocale();
        if (!is_null($locale)) {
            return $locale->getCode();
        }
    }

    /**
     * Assigns the theme's textdomain to Wordpress using 'load_theme_textdomain'.
     * @return boolean The result of load_theme_textdomain
     * @link https://codex.wordpress.org/Function_Reference/load_theme_textdomain
     */
    public function applyLocale()
    {
        $locale = $this->getCurrentLocale();
        $app = Strata::app();

        if (!is_null($locale)) {
            $message =  setlocale(LC_ALL, $locale->getCode() .'.UTF-8') ?
                "Localized to : " . $locale->getCode() . ".UTF-8" :
                "Locale function is not available on this platform, or the given " .
                "local does not exist in this environment. Attempted to set: " .
                $locale->getCode() . ".UTF-8";

            $app->setConfig("runtime.setlocale", $message);
        }

        // Set in PHP
        if (function_exists('bindtextdomain')) {
            $bound = bindtextdomain($this->getTextdomain(), Strata::getLocalePath());
            $app->setConfig("runtime.bindtextdomain", "PHP text domain was bound to <info>$bound</info>");
        }

        if (function_exists('textdomain')) {
            $bound = textdomain($this->getTextdomain());
            $app->setConfig("runtime.textdomain", "PHP message domain was bound to <info>$bound</info>");
        }

        // Set in WP
        return load_theme_textdomain($this->getTextdomain(), Strata::getLocalePath());
    }

    /**
     * Returns the current textdomain as defined either
     * from Strata's configuration or the default value.
     * @return string
     */
    public function getTextdomain()
    {
        $textDomain = Strata::app()->getConfig("i18n.textdomain");
        return is_null($textDomain) ? self::DOMAIN : $textDomain;
    }

    /**
     * Clears the current locale cache and rebuilds it based
     * on the loaded configuration.
     * @return null
     */
    public function resetLocaleCache()
    {
        $this->setLocaleSet(!$this->isLocalized() ? array() : $this->parseLocalesFromConfig());
    }

    /**
     * Sets the set of available locales.
     * @param array $localeList A list of Locale objects
     */
    public function setLocaleSet($localeList)
    {
        $this->locales = $localeList;
    }

    /**
     * Sets the current locale based on either a GET parameter or the Locale URL prefix.
     * If none is found it will return the default locale.
     * @return Locale
     * @filter strata_i18n_set_current_locale_by_context
     */
    public function setCurrentLocaleByContext()
    {
        if (function_exists('apply_filters')) {
            // Give a chance for plugins to override this decision
            $locale = apply_filters('strata_i18n_set_current_locale_by_context', null);
            if (!is_null($locale)) {
                return $this->setLocale($locale);
            }
        }

        if (Router::isAjax() || (function_exists('is_admin') && !is_admin())) {
            $request = new Request();
            if ($request->hasGet("locale")) {
                $locale = $this->getLocaleByCode($request->get("locale"));
                if (!is_null($locale)) {
                    return $this->setLocale($locale);
                }
            }

            // Under ajax, one could post the value.
            if (Router::isAjax()) {
                if ($request->hasPost("locale")) {
                    $locale = $this->getLocaleByCode($request->post("locale"));
                    if (!is_null($locale)) {
                        return $this->setLocale($locale);
                    }
                }

            // when in Frontend and not in ajax, we can guestimate values
            // from the url.
            } else {
                // This validates all locales but the default one
                $urls = implode('|', $this->getLocaleRegexUrls());
                if (preg_match('/^\/('.$urls.')\//i', $_SERVER['REQUEST_URI'], $match)) {
                    $locale = $this->getLocaleByUrl($match[1]);
                    if (!is_null($locale)) {
                        return $this->setLocale($locale);
                    }
                // Also validates for lack of locale code, meaning
                // a possible default locale match when no ajax-ing.
                } elseif (preg_match('/^(?:(?!'.$urls.'))\/?/i', $_SERVER['REQUEST_URI'])) {
                    $locale = $this->getDefaultLocale();
                    if (!is_null($locale)) {
                        return $this->setLocale($locale);
                    }
                }
            }

            if ($this->hasLocaleInSession()) {
                $locale = $this->getLocaleInSession();
                if (!is_null($locale)) {
                    return $this->setLocale($locale);
                }
            }

            if ($this->hasDefaultLocale()) {
                return $this->setLocale($this->getDefaultLocale());
            }
        }
    }

    /**
     * Specifies whether localization settings are present in Strata's
     * configuration array.
     * @return boolean
     */
    public function isLocalized()
    {
        return !is_null(Strata::config("i18n.locales"));
    }

    /**
     * Specifies if the list of active locales
     * has possible elements
     * @return boolean
     */
    public function hasActiveLocales()
    {
        return count($this->getLocales()) > 0;
    }

    /**
     * Returns the list of Locale objects
     * @return array
     */
    public function getLocales()
    {
        return (array)$this->locales;
    }

    /**
     * Returns a Locale object by code
     * @param  string $code The same code used when declaring the locale in the configuration value
     * @return Locale
     */
    public function getLocaleByCode($code)
    {
        $locales = $this->getLocales();
        if (array_key_exists($code, $locales)) {
            return $locales[$code];
        }
    }
    /**
     * Returns a Locale object by locale url key
     * @param  string $url The locale url as set when configuring (defaults to the locale code).
     * @return Locale
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
     * @return string
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
     * Specifies whether the application is currently using the
     * default locale.
     * @return boolean
     */
    public function currentLocaleIsDefault()
    {
        $currentLocale = $this->getCurrentLocale();
        if ($currentLocale) {
            return $currentLocale->isDefault();
        }

        return true;
    }

    /**
     * Checks whether a locale has been stored in the user's session.
     * @return boolean
     */
    public function hasLocaleInSession()
    {
        return array_key_exists($this->getSessionKey(), $_SESSION);
    }

    /**
     * Returns the locale that is saved in the session array.
     * This only returns a value the first time it's called because we don't want to
     * save the session permanently. It's used only to go through 1 page transition.
     * ex: when saving a post in the backend, we lose the locale in the process.
     * @return Locale
     */
    public function getLocaleInSession()
    {
        $localeCode = $_SESSION[$this->getSessionKey()];
        return $this->getLocaleByCode($localeCode);
    }

    /**
     * Starts a PHP session if there was none started already.
     * @return int Session id
     */
    private function startSession()
    {
        $sessionId = session_id();

        if (!$sessionId) {
            return session_start();
        }

        return $sessionId;
    }

    /**
     * The goal of dumping the code in the session vars is only to
     * keep a fallback value when someone (for instance) renders a 404
     * when browsing in a locale. Having the value in session prevents the 404
     * to be rendered with the default locale.
     *
     * The value in session should not have more weight then the other methods
     * in setCurrentLocaleByContext();
     *
     * @see setCurrentLocaleByContext
     */
    public function saveCurrentLocaleToSession()
    {
        $_SESSION[$this->getSessionKey()] = $this->getCurrentLocaleCode();
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
            throw new Exception(sprintf(__("The project has never been scanned for %s.", 'strata'), $locale->getNativeLabel()));
        }

        return Translations::fromPoFile($locale->getPoFilePath());
    }

    /**
     * Saves the translations to .po and .mo files.
     * @param  Locale $locale The locale of the translations
     * @param  array  $postedTranslations A list of translations
     * @return null
     */
    public function saveTranslations(Locale $locale, array $postedTranslations)
    {
        $editedTranslations = $this->postedValuesToTranslation($locale, $postedTranslations);
        $editedTranslations->toPoFile($locale->getPoFilePath(WP_ENV));
        return $this->generateTranslationFiles($locale);
    }

    public function postedValuesToTranslation(Locale $locale, array $postedTranslations)
    {
        $envPoFile = $locale->getPoFilePath(WP_ENV);
        $activeTranslations = $locale->hasPoFile(WP_ENV) ?
            Translations::fromPoFile($envPoFile) :
            new Translations();

        foreach ($postedTranslations as $t) {
            $original = html_entity_decode($t['original']);
            $context = html_entity_decode($t['context']);
            $translationText = html_entity_decode($t['translation']);
            $plural = html_entity_decode($t['pluralTranslation']);

            $translation = $this->addOrCreateString($activeTranslations, $context, $original);
            $translation->setTranslation($translationText);
            $translation->setPluralTranslation($plural);
        }

        return $activeTranslations;
    }

    public function addOrCreateString(Translations $translationSet, $context, $original)
    {
        $translation = $translationSet->find($context, $original);
        if ($translation === false) {
            $translation = new Translation($context, $original, "");
            $translationSet[] = $translation;
        }

        return $translation;
    }

    public function hardTranslationSetMerge(Locale $locale, $from, $into)
    {
        foreach ($from as $translation) {
            $context = $translation->getContext();
            $original = $translation->getOriginal();
            $merged = $this->addOrCreateString($into, $context, $original);
            $merged->setTranslation($translation->getTranslation());
            // The following Raises an array to string warning
            // $merged->setPluralTranslation($translation->getPluralTranslation());
        }

        $into->toPoFile($locale->getPoFilePath(WP_ENV));
    }

    public function generateTranslationFiles(Locale $locale)
    {
        $envPoFile = $locale->getPoFilePath(WP_ENV);
        $poFile = $locale->getPoFilePath();
        $moFile = $locale->getMoFilePath();

        // Load the binary to ensure projects always attempts
        // to compile the same basic string.
        if ($locale->hasMoFile()) {
            $activeTranslations = Translations::fromMoFile($moFile);
        } else {
            $activeTranslations = new Translations();
        }

        // Add local modifications to the default set should there
        // be any.
        if ($locale->hasPoFile(WP_ENV)) {
             $this->hardTranslationSetMerge($locale, Translations::fromPoFile($envPoFile), $activeTranslations);
        }

        $textDomain = Strata::i18n()->getTextdomain();
        $activeTranslations->setDomain($textDomain);
        $activeTranslations->setHeader('Language', $locale->getCode());
        $activeTranslations->setHeader('Text Domain', $textDomain);
        $activeTranslations->setHeader('X-Domain', $textDomain);

        @unlink($poFile);
        @unlink($moFile);

        $activeTranslations->toPoFile($poFile);
        $activeTranslations->toMoFile($moFile);
    }

    /**
     * Compares $locale to the current locale to see if it is currently
     * active.
     * @param  Locale $locale
     * @return boolean
     */
    public function isCurrentlyActive(Locale $locale)
    {
        $current = $this->getCurrentLocale();
        return $locale->getCode() === $current->getCode();
    }

    /**
     * Sets the active locale
     * @param Locale $locale
     * @return Locale
     */
    public function setLocale(Locale $locale)
    {
        $this->currentLocale = $locale;
        return $this->currentLocale;
    }

    /**
     * Returns the localization key in the session array.
     * @return string
     */
    private function getSessionKey()
    {
        if (is_admin() && !Router::isAjax()) {
            return self::DOMAIN . "_admin";
        }

        return self::DOMAIN . "_front";
    }

    /**
     * Registers the Wordpress hooks required by this class.
     * @return null
     */
    protected function registerHooks()
    {
        add_action('after_setup_theme', array($this, "applyLocale"), 1);

        if (Router::isAjax() || !is_admin()) {
            add_filter('locale', array($this, "applyCurrentLanguageByContext"), 999);
        }

        add_action('shutdown', array($this, "saveCurrentLocaleToSession"));
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
     * @return array A list of instantiated Locale object.
     */
    protected function parseLocalesFromConfig()
    {
        $localeInfos = Hash::normalize((array)Strata::config("i18n.locales"));
        $locales = array();

        foreach ($localeInfos as $key => $config) {
            $locales[$key] = new Locale($key, $config);
        }

        return $locales;
    }

    /**
     * Returns the list of urls identifiers of all the
     * active locales
     * @return array
     */
    private function getLocaleRegexUrls()
    {
        $urls = array();
        foreach ($this->getLocales() as $locale) {
            if (!$locale->isDefault()) {
                $urls[] =  preg_quote($locale->getUrl(), '/');
            }
        }
        return $urls;
    }
}
