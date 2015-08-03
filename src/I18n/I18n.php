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

    const DOMAIN = "strata_i18n";

    protected $locales = array();
    protected $currentLocale = null;

    public function initialize()
    {
        $this->resetLocaleCache();
        $this->addLocaleEvents();
    }

    public function addLocaleEvents()
    {

        if (function_exists('add_action')) {
            //$this->addLocaleEndpoints();
            add_filter('locale', array($this, "setAndApplyCurrentLanguageByContext"), 999);
            add_action('after_setup_theme', array($this, "applyLocale"));
        };
    }

    protected function addLocaleEndpoints()
    {
        foreach ($this->getLocales() as $locale) {
            add_rewrite_endpoint($locale->getUrl(), EP_PERMALINK);
        }
    }

    public function setAndApplyCurrentLanguageByContext()
    {
        $this->setCurrentLanguageByContext();
        $locale = $this->getCurrentLocale();

        if (!is_null($locale)) {
            return $locale->getCode();
        }
    }

    public function applyLocale()
    {
        load_theme_textdomain(self::DOMAIN, Strata::getLocalePath());
    }

    public function resetLocaleCache()
    {
        if ($this->hasLocalizationSettings()) {
            $this->createLocalesFromConfig();
        } else {
            $this->locales = array();
        }
    }

    public function setCurrentLanguageByContext()
    {
        $request = new Request();

        if ($request->hasGet("locale")) {
            $locale = $this->getLocaleByCode($request->get("locale"));
            if (!is_null($locale)) {
                $this->setLocale($locale);
                return;
            }
        }

        if ($this->hasDefaultLocale()) {
            $this->setLocale($this->getDefaultLocale());
        }
    }

    public function hasLocalizationSettings()
    {
        return !is_null(Strata::config("i18n.locales"));
    }

    protected function createLocalesFromConfig()
    {
        $locales = Hash::normalize(Strata::config("i18n.locales"));

        foreach ($locales as $key => $config) {
            $this->locales[$key] = new Locale($key, $config);
        }
    }

    protected function setLocale(Locale $locale)
    {
        $this->currentLocale = $locale;
    }

    public function hasActiveLocales()
    {
        return count($this->locales) > 0;
    }

    public function getLocales()
    {
        return $this->locales;
    }

    public function getLocaleByCode($code)
    {
        if (array_key_exists($code, $this->locales)) {
            return $this->locales[$code];
        }
    }

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

    public function isCurrentlyActive(Locale $locale)
    {
        $current = $this->getCurrentLocale();
        return $locale->getCode() === $current->getCode();
    }

    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    public function getCurrentLocaleCode()
    {
        $locale = $this->getCurrentLocale();
        if (!is_null($locale)) {
            return $locale->getCode();
        }
    }

    public function hasDefaultLocale()
    {
        return !is_null($this->getDefaultLocale());
    }

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
}


