<?php

namespace Strata\Model;

use Strata\Core\StrataConfigurableTrait;
use Strata\Utility\Hash;
use Strata\Strata;
use Exception;

/**
 * The mailer object attempts to simplify the generation
 * of application emails.
 */
class Mailer
{
    use StrataConfigurableTrait;

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
     * Sets an email header
     */
    public function setHeader($key, $value)
    {
        $this->setConfig("headers.$key", $value);
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

        $mergedHeaders = $this->getMergedHeaders();

        if ($useHtml) {
            $this->setHtmlEmails();
        }

        $status = wp_mail(
            $this->getConfig("to"),
            $this->getConfig("title"),
            $this->getConfig("contents"),
            $this->toHeaderString($mergedHeaders) . $this->buildBCCString(),
            $this->getConfig("attachedFile")
        );
        

        $logMessage = $status ?
            sprintf("<success>Sent an email</success> to %s with title \"%s\" and a body length of %d characters.", implode(", ", (array)$this->getConfig("to")), $this->getConfig("title"), strlen($this->getConfig("contents"))) :
            sprintf("<warning>Failed to send an email</warning> to %s with title \"%s\" and a body length of %d characters.",  implode(", ", (array)$this->getConfig("to")), $this->getConfig("title"), strlen($this->getConfig("contents")));

        Strata::app()->log($logMessage, "<info>Strata:Mailer</info>");

        if ($useHtml) {
            $this->setHtmlEmails(false);
        }

        return $status;
    }

    protected function getMergedHeaders()
    {
        $defaults = array(
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Mailer' => "PHP " . phpversion()
        );

        return Hash::merge((array)$this->getConfig('headers'), $defaults);
    }

    protected function toHeaderString($headers)
    {
        $return = "";

        foreach ($headers as $key => $value) {
            $return .= sprintf("%s: %s\r\n", $key, $value);
        }

        return $return;
    }

    /**
     * Builds the BCC user list in a format
     * wp_mail can read.
     * @return string
     */
    protected function buildBCCString()
    {
        $adresses = "";

        foreach ((array)$this->getConfig("bcc") as $email) {
            $adresses .= sprintf("Bcc: %s \r\n", trim($email));
        }

        return $adresses;
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
}
