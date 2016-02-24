<?php

namespace Strata\Security;

use Strata\Strata;

/**
 * Wordpress comments, by default, allows one to inject javascript and weird
 * HTML that may break a website.
 */
class CommentParser
{
    /**
     * Registers the filter that handles comment validation if required.
     */
    public function register()
    {
        if ($this->shouldParseComments()) {
            add_filter('preprocess_comment', array($this, 'preprocessComment'));
        }
    }

    /**
     * Checks the configuration file to ensure we are expected to
     * force Strata' comment parser.
     * @return boolean
     */
    protected function shouldParseComments()
    {
        return !(bool)Strata::app()->getConfig("security.ignore_comment_validation");
    }

    /**
     * Converts html in the comment's content into safer HTML entities.
     * @param  array $commentData
     * @return array
     */
    public function preprocessComment($commentData)
    {
        if (array_key_exists('comment_content', $commentData)) {
            $commentData['comment_content'] = htmlentities($commentData['comment_content']);
        }

        return $commentData;
    }
}
