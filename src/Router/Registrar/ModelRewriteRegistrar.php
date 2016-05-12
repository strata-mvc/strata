<?php

namespace Strata\Router\Registrar;

use Strata\Strata;
use Strata\Model\Model;
use Strata\Model\Taxonomy\Taxonomy;

use Exception;

/**
 * Registers rewrite rules defined in model configuration
 * files.
 */
class ModelRewriteRegistrar
{
    private $collectedRewrites = array();
    private $collectedRoutes = array();


    /**
     * Registrar's listener expected to be triggered
     * after the models have been loaded in Wordpress.
     */
    public function onModelsActivated()
    {
        $this->extractRules();
        $this->saveRules();
    }

    /**
     * Add rewrite rules found in the application Custom Post Types'
     * router configuration.
     */
    private function extractRules()
    {
        // Keep in mind we don't support recursive directories (or cache the results)
        $possibleModels = glob(Strata::getSRCPath() . "Model" . DIRECTORY_SEPARATOR . "*.php");
        $this->batchModelFileRegistrationAttempt((array)$possibleModels);

        $possibleTaxonomies = glob(Strata::getSRCPath() . "Model" . DIRECTORY_SEPARATOR . "Taxonomy" . DIRECTORY_SEPARATOR . "*.php");
        $this->batchTaxonomyFileRegistrationAttempt((array)$possibleTaxonomies);
    }

    private function saveRules()
    {
        if (count($this->collectedRewrites)) {
            $rewriter = Strata::rewriter();
            foreach ($this->collectedRewrites as $rewrite) {
                $rewriter->addRule($rewrite[0], $rewrite[1]);
            }
        }

        if (count($this->collectedRoutes)) {
            $router = Strata::router();
            $router->addModelRoutes($this->collectedRoutes);
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
        $routeMaker = null;

        if (
            is_a($model, "\Strata\Model\CustomPostType\CustomPostType") ||
            is_a($model, "\Strata\Model\Taxonomy\Taxonomy")
        ) {
            $routeMaker = new CustomPostTypeRouteMaker();
        } elseif (is_a($model, "\Strata\Model\Model")) {
            $routeMaker = new PageRouteMaker();
        }

        if (!is_null($routeMaker)) {
            $routeMaker->parseObject($model);

            $this->collectedRewrites = array_merge($this->collectedRewrites, $routeMaker->getRewrites());
            $this->collectedRoutes =  array_merge($this->collectedRoutes, $routeMaker->getRoutes());
        }
    }
}
