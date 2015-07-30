<?php
namespace Strata\I18n;

use Strata\Strata;
use Strata\Utility\Hash;
use Strata\I18n\Locale;

use Gettext\Translations;
use Gettext\Translation;

/**
 * Handles  localization
 *
 * @package       Strata.i18n
 */
class i18n {

    protected $locales = array();

    public function initialize()
    {
        $this->resetLocaleCache();
    }

    public function resetLocaleCache()
    {
        if ($this->hasLocalizationSettings()) {
            $this->createLocalesFromConfig();
        } else {
            $this->$locales = array();
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
        $locales = $this->getLocales();

        // if() {
            // Assuming we'll pull the current locale out of somewhere
        //}
        $id = get_the_id();
        foreach ($locales as $locale) {
            if ($locale->wasLocalized() && $locale->getObjId() == $id) {
                return $locale;
            }
        }

        return $this->getDefaultLocale();
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


