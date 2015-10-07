<?php
namespace Strata\Security;

use Strata\Strata;

/**
 * Assigns callback to common WP functions that needs additional security
 *
 * @package Strata.Security
 */
class CommentParser
{

    public function register()
    {
        if ($this->shouldParseComments()) {
            add_filter('preprocess_comment', array($this, 'preprocessComment'));
        }
    }

    protected function shouldParseComments()
    {
        return !(bool)Strata::app()->getConfig("security.ignore_comment_validation");
    }

    public function preprocessComment($commentData)
    {
        if (array_key_exists('comment_content', $commentData)) {
            $commentData['comment_content'] = htmlentities($commentData['comment_content']);
        }

        return $commentData;
    }
}
