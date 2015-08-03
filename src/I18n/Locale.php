<?php
namespace Strata\I18n;

use Strata\Strata;

class Locale {

    protected $nativeLabel;
    protected $code;
    protected $isDefault;
    protected $url;

    function __construct($code, $config = array())
    {
        $this->code = $code;

        // Apply defaults
        $config += array(
            "nativeLabel" => $code,
            "default" => false,
            "url" => null
        );

        $this->url = $config["url"];
        $this->nativeLabel = $config["nativeLabel"];
        $this->isDefault = (bool)$config["default"];
    }

    public function getNativeLabel()
    {
        return $this->nativeLabel;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getUrl()
    {
        if (is_null($this->url)) {
            return $this->getCode();
        }

        return $this->url;
    }

    public function isDefault()
    {
        return (bool)$this->isDefault;
    }

    public function hasANativeLabel()
    {
        return $this->nativeLabel !== $this->code;
    }

    public function hasPoFile()
    {
        return file_exists($this->getPoFilePath());
    }

    public function getPoFilePath()
    {
        $localeDir = Strata::getLocalePath();
        return $localeDir . $this->getCode() . '.po';
    }

    public function hasMoFile()
    {
        return file_exists($this->getMoFilePath());
    }

    public function getMoFilePath()
    {
        $localeDir = Strata::getLocalePath();
        return $localeDir . $this->getCode() . '.mo';
    }

}
