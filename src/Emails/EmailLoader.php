<?php

namespace MVC\Emails;

use MVC;
use MVC\View\Template;

class EmailLoader {

    /**
     * @param string The name of the template to load (.php will be added to it)
     * @param array an associative array of values to assign in the template
     */
    public static function loadTemplate($name, $values = array())
    {
        return Template::render($name, $values);
    }

    // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
    public static function enableHTML()
    {
        return add_filter( 'wp_mail_content_type', 'MVC\Emails\EmailLoader::setContentType' );
    }

    // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
    public static function disableHTML()
    {
        return remove_filter( 'wp_mail_content_type', 'MVC\Emails\EmailLoader::setContentType' );
    }

    /**
     * @todo Expose the list of emails in the configuration file and load it here.
     */
    public static function getEmailAddress($which)
    {
        if (defined('DEBUG_EMAIL')) {
            return DEBUG_EMAIL;
        }

        $emails = MVC\Mvc::config("project_email_list");
        if (is_array($emails) && array_key_exists($which, $emails)) {
            return $emails[$which];
        }

        return null;
    }

    public static function setContentType($which)
    {
        return 'text/html';
    }
}
