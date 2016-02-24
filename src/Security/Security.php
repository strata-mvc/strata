<?php

namespace Strata\Security;

/**
 * Assigns callbacks to common Wordpress functions that needs additional security
 */
class Security
{
    /**
     * Adds every additional security measures Strata thinks
     * should be autoloaded.
     */
    public function addMeasures()
    {
        if (function_exists('add_filter')) {
            $this->handleComments();
        }
    }

    /**
     * Registers the comment parser
     */
    protected function handleComments()
    {
        $parser = new CommentParser();
        $parser->register();
    }
}
