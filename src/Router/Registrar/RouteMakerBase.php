<?php

namespace Strata\Router\Registrar;

use Strata\Strata;
use Exception;
use Strata\Controller\Controller;
use Strata\Utility\Inflector;
use Strata\Utility\Hash;

/**
 * Registers rewrite rules defined in model configuration
 * files.
 */
abstract class RouteMakerBase
{
    /**
     * Keeps a lot of the additional Routing routes
     * that may have been generated while adding custom
     * rewrite rules.
     * @var array
     */
    private $additionalRoutes = array();

    private $additionalRewrites = array();

    // Preprocess the rewrites to group them in
    // a global single regex
    protected $rewriteBatch = array();

    protected $model;

    protected $defaultSlug;

    abstract public function parseObject($model);
    abstract protected function extractLocalizedInformation();
    abstract protected function getRewritePattern();
    abstract protected function getRewriteDestination();
    abstract protected function getRewriteIdentifier();

    public function getRewrites()
    {
        return $this->additionalRewrites;
    }

    public function getRoutes()
    {
        return $this->additionalRoutes;
    }

    protected function isLocalizedApp()
    {
        return Strata::i18n()->isLocalized();
    }

    protected function getLocales()
    {
        return Strata::app()->i18n->getLocales();
    }

    protected function addRoute($route)
    {
        $this->additionalRoutes[] = $route;
    }

    protected function addRewrite($rewrite)
    {
        $this->additionalRewrites[] = $rewrite;
    }

    protected function getController()
    {
        if (array_key_exists("controller", $this->model->routed)) {

            $controllerName = $this->model->routed['controller'];
            return new $controllerName();
        }

        $controllerClass = Controller::generateClassPath($this->model->getShortName());
        return new $controllerClass();
    }

    protected function getAction($controller, $routeKey)
    {
        $impliedAction = lcfirst(Inflector::camelize(str_replace("-", "_", $routeKey)));

        foreach (array($impliedAction, "show", "noActionMatch") as $method) {
            if (method_exists($controller, $method)) {
                return $method;
            }
        }

        return "";
    }

    protected function extractDefaultLocaleInformation()
    {
        $defaultLocale = null;
        if ($this->isLocalizedApp()) {
            $defaultLocale = Strata::i18n()->getDefaultLocale();
        }

        foreach ($this->model->routed['rewrite'] as $routeKey => $routeUrl) {
            $this->queueRewrite($routeUrl, $this->defaultSlug, $defaultLocale);
        }
    }

    protected function saveRewriteBatch()
    {
        foreach ($this->rewriteBatch as $queryVar => $routeConfig) {

            if (count($routeConfig['locale_urls'])) {
                $localeUrlsBit = sprintf("(%s)/", implode("|", $routeConfig["locale_urls"]));
            } else {
                $localeUrlsBit = "";
            }

            $rule = sprintf($this->getRewritePattern(),
                $localeUrlsBit,
                implode("|", $routeConfig["model_slugs"]),
                implode("|", $routeConfig["localized_slugs"])
            );

            $redirect = $this->getRewriteDestination();

            $this->addRewrite(array($rule, $redirect));
        }
    }

    /**
     * Generates a rule readable by the Strata router which
     * will attempt to catch all the extra rewrites.
     * @param  string $routeKey
     * @param  string $slug
     * @return array
     */
    protected function createRouteFor($routeKey, $slug)
    {
        $controller = $this->getController();
        $action = $this->getAction($controller, $routeKey);

        return array(
            'GET|POST|PATCH|PUT|DELETE',
            '/' . $slug ."/[:slug]/[$routeKey:rewrite]/?",
            $controller->getShortName() . "#" . $action
        );
    }

    protected function queueRewrite($routeUrl, $slug, $locale = null)
    {
        $queryVar = $this->getRewriteIdentifier();

        $localeUrl = null;
        if (!is_null($locale) && $locale->hasACustomUrl()) {
            $localeUrl = $locale->getUrl();
        }

        if (!array_key_exists($queryVar, $this->rewriteBatch)) {
            $this->rewriteBatch[$queryVar] = array(
                "model_slugs" => array(),
                "locale_urls" => array(),
                "localized_slugs" => array(),
            );
        }

        if (!is_null($localeUrl) && !in_array($localeUrl, $this->rewriteBatch[$queryVar]["locale_urls"])) {
            $this->rewriteBatch[$queryVar]["locale_urls"][] = $localeUrl;
        }

        if (!in_array($routeUrl, $this->rewriteBatch[$queryVar]["localized_slugs"])) {
            $this->rewriteBatch[$queryVar]["localized_slugs"][] = $routeUrl;
        }

        if (!in_array($slug, $this->rewriteBatch[$queryVar]["model_slugs"])) {
            $this->rewriteBatch[$queryVar]["model_slugs"][] = $slug;
        }
    }
}
