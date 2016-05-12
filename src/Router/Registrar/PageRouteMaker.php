<?php

namespace Strata\Router\Registrar;

use Strata\Utility\Hash;
use Exception;

/**
 * Registers rewrite rules defined in model configuration
 * files.
 */
class PageRouteMaker extends RouteMakerBase
{
    public function parseObject($model)
    {
        if (property_exists($model, "routed") && is_array($model->routed)) {
            if (array_key_exists("page_slug_regex", $model->routed)) {
                $this->model = $model;
                $this->defaultSlug = $model->routed["page_slug_regex"];

                $this->extractDefaultLocaleInformation();

                if ($this->isLocalizedApp()) {
                    $this->extractLocalizedInformation();
                }

                $this->saveRewriteBatch();
            }
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
            '/' . $this->defaultSlug ."/[$routeKey:rewrite]/?",
            $controller->getShortName() . "#" . $action
        );
    }

    protected function extractLocalizedInformation()
    {
        foreach ($this->getLocales() as $locale) {
            $localeCode = $locale->getCode();
            foreach ($this->model->routed['rewrite'] as $routeKey => $routeUrl) {
                $localizedUrl = $routeUrl;
                $localizedPath = "i18n.$localeCode.rewrite.$routeKey";

                if (Hash::check($this->model->routed, $localizedPath)) {
                    $localizedUrl = Hash::get($this->model->routed, $localizedPath);
                }

                $this->addRoute($this->createRouteFor($localizedUrl, $this->defaultSlug));
                $this->queueRewrite($localizedUrl, $this->defaultSlug, $locale);
            }
        }
    }

    protected function getRewriteIdentifier()
    {
        return "pagename";
    }

    protected function getRewritePattern()
    {
        return "%s%s/(%s)/?$";
    }

    protected function getRewriteDestination()
    {
        return sprintf('index.php?%s=$matches[2]&locale=$matches[1]', $this->getRewriteIdentifier());
    }
}
