<?php
namespace Strata\Security;

/**
 * Assigns callback to common WP functions that needs additional security
 *
 * @package       Strata.Security
 */
class Security {

    public function addMesures()
    {
        $this->handleComments();
    }

    protected function handleComments()
    {
        $parser = new CommentParser();
        $parser->register();
    }
}


