<?php
namespace Strata\Router\Registrar;

use Strata\Utility\Hash;

/**
 * Registers rewrite rules defined in model configuration
 * files.
 */
class CustomPostTypeRouteMaker extends RouteMakerBase
{
    public function parseObject($model)
    {
        $this->model = $model;
        $this->defaultSlug = $model->getConfig('rewrite.slug');

        if (is_array($this->model->routed)) {

            // This seems inelegant.
            if (is_a($model, "Strata\Model\Post") && array_key_exists("page_slug_regex", $model->routed)) {
                $this->addRoute(array(
                    '*',
                    '/' . $model->routed["page_slug_regex"] ."/?",
                    $this->getController()->getShortName() . "#index"
                ));
            }

            $this->extractDefaultLocaleInformation();

            if ($this->isLocalizedApp()) {
                $this->extractLocalizedInformation();
            }

            $this->saveRewriteBatch();
        }
    }

    protected function extractLocalizedInformation()
    {
        if ($this->modelSupportsRewrites()) {
            foreach ($this->getLocales() as $locale) {

                $localeCode = $locale->getCode();
                $localizedSlug = $this->model->hasConfig("i18n.$localeCode.rewrite.slug") ?
                    $this->model->getConfig("i18n.$localeCode.rewrite.slug") :
                    $this->defaultSlug;

                foreach ($this->model->routed['rewrite'] as $routeKey => $routeUrl) {

                    $localizedUrl = $routeUrl;
                    $localizedPath = "i18n.$localeCode.rewrite.$routeKey";

                    if (Hash::check($this->model->routed, $localizedPath)) {
                        $localizedUrl = Hash::get($this->model->routed, $localizedPath);
                    }

                    $this->addRoute($this->createRouteFor($localizedUrl, $localizedSlug));
                    $this->queueRewrite($localizedUrl, $localizedSlug, $locale);
                }
            }
        }
    }

    protected function getRewriteIdentifier()
    {
        return $this->model->getQueryVar();
    }

    protected function getRewritePattern()
    {
        return "%s(%s)/([^/]+)/(%s)/?$";
    }

    protected function getRewriteDestination()
    {
        return sprintf('index.php?%s=$matches[3]&locale=$matches[1]', $this->getRewriteIdentifier());
    }
}
