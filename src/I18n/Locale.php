<?php
namespace Strata\I18n;

use Strata\Strata;

class Locale
{

    protected $nativeLabel;
    protected $code;
    protected $isDefault;
    protected $url;
    protected $config = array();

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

        // Save the rest
        $this->config = $config;
    }

    public function getConfig($key)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
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

    public function hasPoFile($env = null)
    {
        return file_exists($this->getPoFilePath($env));
    }

    public function getPoFilePath($env = null)
    {
        $localeDir = Strata::getLocalePath();

        if (!is_null($env)) {
            return $localeDir . $this->getCode() . '-' . $env . '.po';
        }

        return $localeDir . $this->getCode() . '.po';
    }

    public function hasMoFile($env = null)
    {
        return file_exists($this->getMoFilePath($env));
    }

    public function getMoFilePath($env)
    {
        $localeDir = Strata::getLocalePath();

        if (!is_null($env)) {
            return $localeDir . $this->getCode() . '-' . $env . '.mo';
        }

        return $localeDir . $this->getCode() . '.mo';
    }
}
