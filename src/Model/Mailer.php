<?php

namespace Strata\Model;

use Strata\Core\StrataConfigurableTrait;
use Exception;

/**
 * The mailer object attempts to simplify the generation
 * of application emails.
 */
class Mailer
{
    use StrataConfigurableTrait;

    /**
     * @var array The default email headers
     */
    protected $headers = array('Content-Type: text/html; charset=UTF-8');

    /**
     * Initiates the Mailer with common default
     * values.
     */
    public function init()
    {
        $this->configure(array(
            "use_html" => true,
            "title" => "",
            "attachedFile" => null,
            "to" => array(),
            "bcc" => array(),
            "email_separator" => ";"
        ));
    }

    /**
     * Sets the email title
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->setConfig("title", $title);
    }

    /**
     * Sets the email destination
     * @param mixed $to
     */
    public function setDestination($to)
    {
        $this->setConfig("to", $to);
    }

    /**
     * Sets the email contents
     * @param string $contents
     */
    public function setContent($contents)
    {
        $this->setConfig("contents", $contents);
    }

    /**
     * Sets the email headers
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->setConfig("headers", $headers);
    }

    /**
     * Attaches a file to the email
     * @param  string $filePath
     */
    public function attachFile($filePath)
    {
        $this->setConfig("attachedFile", $filePath);
    }

    /**
     * Sends the email and returns the delivery status
     * @return boolean
     */
    public function send()
    {
        $useHtml = (bool)$this->getConfig("use_html");

        $mergedHeaders = array_merge(
            $this->headers,
            $this->buildBCCList()
        );

        if ($useHtml) {
            $this->setHtmlEmails();
        }

        $status = wp_mail(
            $this->getConfig("to"),
            $this->getConfig("title"),
            $this->getConfig("contents"),
            $mergedHeaders,
            $this->getConfig("attachedFile")
        );

        if ($useHtml) {
            $this->setHtmlEmails(false);
        }

        return $status;
    }

    /**
     * Sets the email's content type to HTML.
     */
    public function setHtmlContentType()
    {
        return 'text/html';
    }

    /**
     * Registers filters to enable or disable HTML as the email
     * content type
     * @param boolean $enable
     */
    protected function setHtmlEmails($enable = true)
    {
        if ($enable) {
            add_filter('wp_mail_content_type', array($this, 'setHtmlContentType'));
        } else {
            remove_filter('wp_mail_content_type', array($this, 'setHtmlContentType'));
        }
    }

    /**
     * Builds the BCC user list in a format
     * wp_mail can read.
     * @return array
     */
    protected function buildBCCList()
    {
        $adresses = array();
        $bcc = $this->getConfig("bcc");

        foreach ((array)$bcc as $email) {
            $adresses[] = 'Bcc: ' . trim($email);
        }

        return $adresses;
    }
}
