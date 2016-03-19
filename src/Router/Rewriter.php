<?php

namespace Strata\Router;

use Exception;

use Strata\Strata;
use Strata\Router\RouteParser\Alto\AltoRouteParser;
use Strata\Router\RouteParser\Callback\CallbackRouter;

/**
 * Declares new rewrite rules within Wordpress
 */
class Rewriter
{
    /**
     * Caches the rewrite rules defined at runtime.
     * @var array
     */
    private $rewrites = array();

    /**
     * Initialize the rewriter.
     */
    public function initialize()
    {
        add_action('init', array($this, "applyRules"), 2);
    }

    /**
     * Denotes whether rules have been defined.
     * @return boolean
     */
    public function hasRules()
    {
        return count($this->getRules());
    }

    /**
     * Returns the list of defined rewrites.
     * @return array
     */
    public function getRules()
    {
        return (array)$this->rewrites;
    }

    /**
     * Adds a rule to the stack
     * @param string $regex
     * @param string $mapping
     * @param string $priority Defaults to 'top'
     */
    public function addRule($regex, $mapping, $priority = 'top')
    {
        $this->rewrites[] = array($regex, $mapping, $priority);
    }

    /**
     * Applies the rules that have been defined at runtime. Note that
     * the rules are added in the same order as they have been defined
     * and this may cause racing condition in how rules are applied.
     * @return boolean Returns whether the process has written in the database
     */
    public function applyRules()
    {
        if (!$this->hasRules()) {
            return false;
        }

        $rules = $this->getRules();
        foreach ($rules as $rewrite) {
            add_rewrite_rule($rewrite[0], $rewrite[1], 'top');
        }

        $wroteInDB = $this->flush();

        $message = $wroteInDB ?
            sprintf("Added <info>%d</info> and flushed the rewrite list.", count($rules)) :
            sprintf("Added <info>%d</info> while using the cached rewrites.", count($rules));

        Strata::app()->log($message, "<info>Rewriter</info>");

        return $wroteInDB;
    }

    /**
     * Returns a hash representing the current rewrite configuration.
     * @return string
     */
    public function getCurrentConfigurationState()
    {
        return md5(json_encode($this->rewrites));
    }

    /**
     * Flushes the rewrite rules to the database if the rules look
     * like they have not been saved before hand.
     * @return boolean True if Strata wrote in the database
     */
    private function flush()
    {
        $currentConfigurationHash = $this->getCurrentConfigurationState();
        if ($currentConfigurationHash !== get_option('strata_rewrite_state')) {
            update_option('strata_rewrite_state', $currentConfigurationHash);
            flush_rewrite_rules();
            return true;
        }

        return false;
    }
}
