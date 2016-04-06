<?php

namespace Strata\I18n;

use Strata\Strata;
use Strata\Core\StrataConfigurableTrait;

/**
 * A localized language object that contains
 * custom configuration and a language code.
 */
class Locale
{
    use StrataConfigurableTrait;

    /**
     * @var string The label of the language in its native language ex: English, Français
     */
    protected $nativeLabel;

    /**
     * @var string The unique locale code
     */
    protected $code;

    /**
     * @var boolean Specifies whether the locale is the application's default language.
     */
    protected $isDefault;

    /**
     * @var string The unique URL prefix of the locale, used to switch in between languages.
     */
    protected $url;

    /**
     * A Locale requires a unique ISO $code and can be configured with
     * any other custom values.
     * @param string $code   A unique language code
     * @param array  $config [description]
     */
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
        $this->configure($config);
    }

    /**
     * Returns the locale label in its own translation.
     * @return string
     */
    public function getNativeLabel()
    {
        return $this->nativeLabel;
    }

    /**
     * Returns the locale code
     * @return [type] [description]
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns the unique Locale URL prefix.
     * @return string
     */
    public function getUrl()
    {
        if (is_null($this->url)) {
            return $this->getCode();
        }

        return $this->url;
    }

    public function getHomeUrl()
    {
        if ($this->isDefault() && $this->hasACustomUrl() || !$this->isDefault()) {
            return get_home_url() . "/" . $this->getUrl() . "/";
        }

        return get_home_url() . "/";
    }

    /**
     * Specifies whether this locale is the default one.
     * @return boolean
     */
    public function isDefault()
    {
        return (bool)$this->isDefault;
    }

    /**
     * Native labels being optional, this specifies
     * whether this locale has a native label.
     * @return boolean
     */
    public function hasANativeLabel()
    {
        return $this->nativeLabel !== $this->code;
    }

    /**
     * @return boolean
     */
    public function hasACustomUrl()
    {
        return !is_null($this->url) || !$this->isDefault();
    }

    /**
     * Specifies whether this locale has generated a
     * .PO file.
     * @param  string  $env (optional) An environment
     * @return boolean
     */
    public function hasPoFile($env = null)
    {
        return file_exists($this->getPoFilePath($env));
    }

    /**
     * Returns the PO file path either by default or for
     * a specified environment
     * @param  string $env (optional) An environment
     * @return string
     */
    public function getPoFilePath($env = null)
    {
        $localeDir = Strata::getLocalePath();

        if (!is_null($env)) {
            return $localeDir . $this->getCode() . '-' . $env . '.po';
        }

        return $localeDir . $this->getCode() . '.po';
    }

    /**
     * Specifies whether this locale has generated a
     * .Mo file.
     * @param  string  $env (optional) An environment
     * @return boolean
     */
    public function hasMoFile($env = null)
    {
        return file_exists($this->getMoFilePath($env));
    }

    /**
     * Returns the PO file path either by default or for
     * a specified environment
     * @param  string $env (optional) An environment
     * @return string
     */
    public function getMoFilePath($env = null)
    {
        $localeDir = Strata::getLocalePath();

        if (!is_null($env)) {
            return $localeDir . $this->getCode() . '-' . $env . '.mo';
        }

        return $localeDir . $this->getCode() . '.mo';
    }
}
