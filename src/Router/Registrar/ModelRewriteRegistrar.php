<?php

namespace Strata\Router\Registrar;

use Strata\Strata;
use Strata\Utility\Hash;
use Strata\Utility\Inflector;
use Strata\Controller\Controller;
use Strata\Model\WordpressEntity;

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
        foreach (get_post_types(null, "objects") as $cptKey => $cptActiveConfig) {
            $this->attemptRuleExtraction($cptKey, $cptActiveConfig);
        }

        foreach (get_taxonomies(array(), "objects") as $taxonomyKey => $taxonomyActiveConfig) {
            $this->attemptRuleExtraction($taxonomyKey, $taxonomyActiveConfig);
        }

        if (count($this->additionalRoutes)) {
            Strata::router()->addModelRoutes($this->additionalRoutes);
        }

        if (count($this->additionalRewrites)) {
            $this->saveAdditionalRewrites();
        }
    }

    private function attemptRuleExtraction($key, $config)
    {
        if (property_exists($config, 'rewrite') && isset($config->rewrite['slug'])) {
            $slug = $config->rewrite['slug'];
            $model = WordpressEntity::factoryFromKey($key);
            if (!is_null($model) && is_array($model->routed)) {
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

            $rewriter = Strata::app()->rewriter;
            $i18n = Strata::i18n();
            $defaultLocalePrefix = "";

            if ($i18n->isLocalized()) {
                $defaultLocale = $i18n->getDefaultLocale();
                if ($defaultLocale->hasACustomUrl()) {
                    $defaultLocalePrefix = $defaultLocale->getUrl();
                }
            }

            foreach ($model->routed['rewrite'] as $routeKey => $routeUrl) {
                $this->queueAdditionalRewrite($routeKey, $defaultLocalePrefix, $routeUrl, $slug, $model->getQueryVar());

                $preciseRoute = $this->createRouteFor($model, $routeKey, $slug);
                if (!is_null($preciseRoute)) {
                    $this->additionalRoutes[] = $preciseRoute;
                }
            }

            $this->additionalRoutes[] = $this->createGeneralRouteFor($model, $routeKey, $slug);
        }
    }

    /**
     * Adds rewrite rules based on the explicitly defined locale.
     * @param CustomPostType $model
     * @param string $slug
     */
    private function addLocalizedRewrites($model, $slug)
    {
        if (array_key_exists('i18n', $model->routed)) {
            $app = Strata::app();
            $i18n = $app->i18n;

            foreach ($model->routed['i18n'] as $localeCode => $localizedRouteInfo) {
                $locale = $i18n->getLocaleByCode($localeCode);

                $localizedSlug = $model->hasConfig("i18n.$localeCode.rewrite.slug") ?
                    $model->getConfig("i18n.$localeCode.rewrite.slug") :
                    $slug;

                if (!is_null($locale) && array_key_exists('rewrite', $localizedRouteInfo)) {
                    foreach ($localizedRouteInfo['rewrite'] as $originalKey => $translatedUrl) {

                        $this->queueAdditionalRewrite($originalKey, $locale->getUrl(), $translatedUrl, $localizedSlug, $model->getQueryVar());

                        $preciseRoute = $this->createRouteFor($model, $translatedUrl, $locale->getUrl() . '/' . $slug);
                        if (!is_null($preciseRoute)) {
                            $this->additionalRoutes[] = $preciseRoute;
                        }
                    }

                    $this->additionalRoutes[] = $this->createGeneralRouteFor($model, $translatedUrl, $locale->getUrl() . '/' . $slug);
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
    private function createGeneralRouteFor($model, $routeKey, $slug)
    {
        $controller = Controller::generateClassName($model->getShortName());
        $controllerClass = Controller::generateClassPath($model->getShortName());
        $impliedAction = lcfirst(Inflector::camelize($routeKey));
        $action = null;

        foreach (array($impliedAction, "show", "noRouteMatch") as $method) {
            if (method_exists($controllerClass, $method)) {
                $action = $method;
                break;
            }
        }

        return array(
            'GET|POST|PATCH|PUT|DELETE',
            '/' . $slug ."/[:slug]/[:rewrite]/?",
            "$controller#" . $action
        );
    }

    /**
     * Generated a rule readable by the Strata router which
     * will attempt to catch a precise rewrite forwarded to
     * an explicit action.
     * @param  CustomPostType $model
     * @param  string $routeKey
     * @param  string $slug
     * @return array
     */
    private function createRouteFor($model, $routeKey, $slug)
    {
        $controller = Controller::generateClassName($model->getShortName());
        $controllerClass = Controller::generateClassPath($model->getShortName());
        $action = $routeKey;

        if (method_exists($controllerClass, $action)) {
            return array(
                'GET|POST|PATCH|PUT|DELETE',
                '/' . $slug ."/[:slug]/[$routeKey:rewrite]/?",
                "$controller#" . $action
            );
        }
    }

    private function queueAdditionalRewrite($routeKey, $localeUrl, $routeUrl, $slug, $wordpressKey)
    {
        if (!array_key_exists($routeKey, $this->additionalRewrites)) {
            $this->additionalRewrites[$routeKey] = array(
                "model_entity" => $wordpressKey,
                "model_slugs" => array(),
                "locale_urls" => array(),
                "localized_slugs" => array(),
            );
        }

        if (!in_array($localeUrl, $this->additionalRewrites[$routeKey]["locale_urls"])) {
            $this->additionalRewrites[$routeKey]["locale_urls"][] = $localeUrl;
        }

        if (!in_array($routeUrl, $this->additionalRewrites[$routeKey]["localized_slugs"])) {
            $this->additionalRewrites[$routeKey]["localized_slugs"][] = $routeUrl;
        }

        if (!in_array($slug, $this->additionalRewrites[$routeKey]["model_slugs"])) {
            $this->additionalRewrites[$routeKey]["model_slugs"][] = $slug;
        }
    }

    private function saveAdditionalRewrites()
    {
        $rewriter = Strata::rewriter();

        foreach ($this->additionalRewrites as $routeKey => $routeConfig) {
            $rule = sprintf('(%s)/(%s)/([^/]+)/(%s)/?$',
                implode("|", $routeConfig["locale_urls"]),
                implode("|", $routeConfig["model_slugs"]),
                implode("|", $routeConfig["localized_slugs"])
            );
            $redirect = sprintf('index.php?%s=$matches[3]&locale=$matches[1]', $routeConfig["model_entity"]);
            $rewriter->addRule($rule, $redirect);
        }
    }
}
