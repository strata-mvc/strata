<?php
namespace Strata\Model;

use Strata\Core\StrataConfigurableTrait;
use Exception;

abstract class Mailer
{
    use StrataConfigurableTrait;

    abstract public function setDestination($destination);

    protected $headers = array('Content-Type: text/html; charset=UTF-8');

    public function init()
    {
        $this->configure(array(
            "use_html" => true,
            "title" => "",
            "attachedFile" => null,
            "to" => array(),
            "bcc" => array()
        ));
    }

    public function setTitle($title)
    {
        $this->setConfig("title", $title);
    }

    public function setContent($contents)
    {
        $this->setConfig("contents", $contents);
    }

    public function setHeaders($headers)
    {
        $this->setConfig("headers", $headers);
    }

    public function attachFile($filePath)
    {
        $this->setConfig("attachedFile", $filePath);
    }

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

    public function setHtmlContentType()
    {
        return 'text/html';
    }

    protected function setHtmlEmails($enable = true)
    {
        if ($enable) {
            add_filter('wp_mail_content_type', array($this, 'setHtmlContentType'));
        } else {
            remove_filter('wp_mail_content_type', array($this, 'setHtmlContentType'));
        }
    }

    protected function buildBCCList($separator = ";")
    {
        $adresses = array();
        $bcc = $this->getConfig("bcc");

        foreach ((array)$bcc as $email) {
            $adresses[] = 'Bcc: ' . trim($email);
        }

        return $adresses;
    }
}
