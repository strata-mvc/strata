<?php

namespace Strata\Router\Registrar;

use Strata\Strata;
use Strata\Utility\Hash;
use Strata\Controller\Controller;
use Strata\Model\CustomPostType\CustomPostType;

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
            if (property_exists($cptActiveConfig, 'rewrite') && isset($cptActiveConfig->rewrite['slug'])) {
                $slug = $cptActiveConfig->rewrite['slug'];
                $model = CustomPostType::factoryFromKey($cptKey);
                if (!is_null($model) && is_array($model->routed)) {
                    $this->addDefaultRewrites($model, $slug);
                    $this->addLocalizedRewrites($model, $slug);
                }
            }
        }

        if (count($this->additionalRoutes)) {
            Strata::app()->router->addModelRoutes($this->additionalRoutes);
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
                    $defaultLocalePrefix = $defaultLocale->getUrl() . "/";
                }
            }

            foreach ($model->routed['rewrite'] as $routeKey => $routeUrl) {
                $rule = sprintf('%s%s/([^/]+)/%s/?$', $defaultLocalePrefix, $slug, $routeUrl);
                $redirect = sprintf('index.php?%s=$matches[1]', $model->getWordpressKey());
                $rewriter->addRule($rule, $redirect);

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
            $rewriter = $app->rewriter;
            $i18n = $app->i18n;

            foreach ($model->routed['i18n'] as $localeCode => $localizedRouteInfo) {
                $locale = $i18n->getLocaleByCode($localeCode);
                if (!is_null($locale) && array_key_exists('rewrite', $localizedRouteInfo)) {
                    foreach ($localizedRouteInfo['rewrite'] as $originalKey => $translatedUrl) {

                        $rule = sprintf('%s/%s/([^/]+)/%s/?$', $locale->getUrl(), $slug, $translatedUrl);
                        $redirect = sprintf('index.php?%s=$matches[1]', $model->getWordpressKey());
                        $rewriter->addRule($rule, $redirect);


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
        $action = "show";

        if (!method_exists($controllerClass, $action)) {
            $action = "noRouteMatch";
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
}
