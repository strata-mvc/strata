<?php

namespace Strata\Router\Registrar;

use Strata\Strata;
use Strata\Utility\Hash;
use Strata\Utility\Inflector;
use Strata\Controller\Controller;
use Strata\Model\WordpressEntity;
use Strata\Model\Model;
use Strata\Model\Taxonomy\Taxonomy;
use Exception;

/**
 * Registers rewrite rules defined in model configuration
 * files.
 */
class ModelRewriteRegistrar
{
    /**
     * Keeps a lot of the additional Routing routes
     * that may have been generated while adding custom
     * rewrite rules.
     * @var array
     */
    private $additionalRoutes = array();

    private $additionalRewrites = array();

    /**
     * Registrar's listener expected to be triggered
     * after the models have been loaded in Wordpress.
     */
    public function onModelsActivated()
    {
        $this->addRules();
    }

    /**
     * Add rewrite rules found in the application Custom Post Types'
     * router configuration.
     */
    private function addRules()
    {
        // Keep in mind we don't support recursive directories (or cache the results)
        $possibleModels = glob(Strata::getSRCPath() . "Model" . DIRECTORY_SEPARATOR . "*.php");
        $this->batchModelFileRegistrationAttempt((array)$possibleModels);

        $possibleTaxonomies = glob(Strata::getSRCPath() . "Model" . DIRECTORY_SEPARATOR . "Taxonomy" . DIRECTORY_SEPARATOR . "*.php");
        $this->batchTaxonomyFileRegistrationAttempt((array)$possibleTaxonomies);

        if (count($this->additionalRoutes)) {
            Strata::router()->addModelRoutes($this->additionalRoutes);
        }

        if (count($this->additionalRewrites)) {
            $this->saveAdditionalRewrites();
        }
    }

    private function batchModelFileRegistrationAttempt($files = array())
    {
        foreach ($files as $filename) {
            try {
                $potentialClassName = basename(substr($filename, 0, -4));
                $model = Model::factory($potentialClassName);
                $this->attemptRuleExtraction($model);
            } catch (Exception $e) {
                // falure only means its not a Strata model.
            }
        }
    }

    private function batchTaxonomyFileRegistrationAttempt($files = array())
    {
        foreach ($files as $filename) {
            try {
                $potentialClassName = basename(substr($filename, 0, -4));
                $model = Taxonomy::factory($potentialClassName);
                $this->attemptRuleExtraction($model);
            } catch (Exception $e) {
                // falure only means its not a Strata model.
            }
        }
    }

    private function attemptRuleExtraction($model)
    {
        if (method_exists($model, "hasConfig") && $model->hasConfig('rewrite.slug')) {
            $slug = $model->getConfig('rewrite.slug');
            if (is_array($model->routed)) {
                $this->addDefaultRewrites($model, $slug);
                $this->addLocalizedRewrites($model, $slug);
            }
        }
    }

    /**
     * Adds rewrite rules based on the default locale.
     * @param CustomPostType $model
     * @param string $slug
     */
    private function addDefaultRewrites($model, $slug)
    {
        if (array_key_exists('rewrite', $model->routed)) {
            $i18n = Strata::i18n();
            $defaultLocalePrefix = "";

            if ($i18n->isLocalized()) {
                $defaultLocale = $i18n->getDefaultLocale();
                if ($defaultLocale->hasACustomUrl()) {
                    $defaultLocalePrefix = $defaultLocale->getUrl();
                }
            }

            foreach ($model->routed['rewrite'] as $routeKey => $routeUrl) {
                $this->queueAdditionalRewrite($defaultLocalePrefix, $routeUrl, $slug, $model->getQueryVar());
                $this->additionalRoutes[] = $this->createRouteFor($model, $routeKey, $slug);
            }
        }
    }

    /**
     * Adds rewrite rules based on the explicitly defined locale.
     * @param CustomPostType $model
     * @param string $slug
     */
    private function addLocalizedRewrites($model, $slug)
    {
        $app = Strata::app();
        $i18n = $app->i18n;
        $queryVar = $model->getQueryVar();

        foreach ($i18n->getLocales() as $locale) {
            $localeCode = $locale->getCode();

            $localizedSlug = $model->hasConfig("i18n.$localeCode.rewrite.slug") ?
                $model->getConfig("i18n.$localeCode.rewrite.slug") :
                $slug;


            $defaults = Hash::get($model->routed, "rewrite");
            if (is_array($defaults)) {
                foreach ($defaults as $defaultKey => $defaultUrl) {

                    $localizedUrl = $defaultUrl;
                    if (Hash::check($model->routed, "i18n.$localeCode.rewrite.$defaultKey")) {
                        $localizedUrl = Hash::get($model->routed, "i18n.$localeCode.rewrite.$defaultKey");
                    }

                    $this->queueAdditionalRewrite($locale->getUrl(), $localizedUrl, $localizedSlug, $queryVar);
                    $this->additionalRoutes[] = $this->createRouteFor($model, $localizedUrl, $localizedSlug);
                }
            }
        }
    }

    /**
     * Generated a rule readable by the Strata router which
     * will attempt to catch all the extra rewrites.
     * @param  CustomPostType $model
     * @param  string $routeKey
     * @param  string $slug
     * @return array
     */
    private function createRouteFor($model, $routeKey, $slug)
    {
        $controller = Controller::generateClassName($model->getShortName());
        $controllerClass = Controller::generateClassPath($model->getShortName());
        $impliedAction = lcfirst(Inflector::camelize(str_replace("-", "_", $routeKey)));
        $action = null;

        foreach (array($impliedAction, "show", "noRouteMatch") as $method) {
            if (method_exists($controllerClass, $method)) {
                $action = $method;
                break;
            }
        }

        return array(
            'GET|POST|PATCH|PUT|DELETE',
            '/' . $slug ."/[:slug]/[$routeKey:rewrite]/?",
            "$controller#" . $action
        );
    }

    private function queueAdditionalRewrite($localeUrl, $routeUrl, $slug, $wordpressKey)
    {
        if (!array_key_exists($wordpressKey, $this->additionalRewrites)) {
            $this->additionalRewrites[$wordpressKey] = array(
                "model_slugs" => array(),
                "locale_urls" => array(),
                "localized_slugs" => array(),
            );
        }

        if (!in_array($localeUrl, $this->additionalRewrites[$wordpressKey]["locale_urls"])) {
            $this->additionalRewrites[$wordpressKey]["locale_urls"][] = $localeUrl;
        }

        if (!in_array($routeUrl, $this->additionalRewrites[$wordpressKey]["localized_slugs"])) {
            $this->additionalRewrites[$wordpressKey]["localized_slugs"][] = $routeUrl;
        }

        if (!in_array($slug, $this->additionalRewrites[$wordpressKey]["model_slugs"])) {
            $this->additionalRewrites[$wordpressKey]["model_slugs"][] = $slug;
        }
    }

    private function saveAdditionalRewrites()
    {
        $rewriter = Strata::rewriter();

        foreach ($this->additionalRewrites as $wordpressKey => $routeConfig) {
            $rule = sprintf('(%s)/(%s)/([^/]+)/(%s)/?$',
                implode("|", $routeConfig["locale_urls"]),
                implode("|", $routeConfig["model_slugs"]),
                implode("|", $routeConfig["localized_slugs"])
            );
            $redirect = sprintf('index.php?%s=$matches[3]&locale=$matches[1]', $wordpressKey);
            $rewriter->addRule($rule, $redirect);
        }
    }
}
