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
    const REDIRECT_KEY = "disable_redirect";

    protected $locales = array();
    protected $currentLocale = null;

    public function initialize()
    {
        $this->resetLocaleCache();
        $this->addLocaleEvents();
    }

    public function addLocaleEvents()
    {
        if (function_exists('add_action') && $this->hasLocalizationSettings()) {
            add_action('generate_rewrite_rules', array($this, 'addLocalePrefixes'));
            add_filter('locale', array($this, "setAndApplyCurrentLanguageByContext"), 999);
            add_action('after_setup_theme', array($this, "applyLocale"));
            add_filter('wp_redirect',  array($this, 'disableRedirect'));
            add_filter('query_vars', array($this, 'addQueryVars'));
        };
    }

    public function addQueryVars($qv)
    {
        $qv[] = self::REDIRECT_KEY;
        return $qv;
    }


    function disableRedirect($location)
    {
        $disable_redirect = get_query_var(self::REDIRECT_KEY);
        if(!empty($disable_redirect)) return false;
        return $location;
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

    public function addLocalePrefixes($wpRewrite)
    {
        $keys = array();
        foreach ($this->getLocales() as $locale) {
            $keys[] = $locale->getUrl();
        }

        $wpRewrite->pagination_base = __('page', self::DOMAIN);
        $wpRewrite->author_base = __('author', self::DOMAIN);
        $wpRewrite->comments_base = __('comments', self::DOMAIN);
        $wpRewrite->feed_base = __('feed', self::DOMAIN);
        $wpRewrite->search_base = __('search', self::DOMAIN);
        $wpRewrite->set_category_base( __('category', self::DOMAIN) . "/");
        $wpRewrite->set_tag_base( __('tag', self::DOMAIN) . "/" );

        $newrules = array();
        foreach ($wpRewrite->rules as $key => $rule) {

            if (strstr($key, "index.php/")) {
                $newKey = str_replace('index.php/', 'index.php/(' . implode("|", $keys)  . ")/", $key);
            } else {
                $newKey = '(' . implode("|", $keys)  . ')/' . $key;
            }

            $bumpedString = preg_replace_callback("/matches\[(\d+)\]/", function($matches) {
                return "matches[".((int)$matches[1]+1)."]";
            }, $rule);


            $newRule = str_replace('index.php?', 'index.php?locale=$matches[1]&'.self::REDIRECT_KEY.'=1&', $bumpedString);
            $newrules[$newKey] = $newRule;
        }

        $wpRewrite->rules = $newrules + $wpRewrite->rules;
        return $wpRewrite->rules;
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

        $keys = array();
        foreach ($this->getLocales() as $locale) {
            $keys[] = $locale->getUrl();
        }
        if (preg_match('/\/('.implode('|', $keys).')\//i', $_SERVER['REQUEST_URI'], $match)) {
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

    public function getLocaleByUrl($url)
    {
        foreach ($this->getLocales() as $locale) {
            if ($locale->getUrl() === $url) {
                return $locale;
            }
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
