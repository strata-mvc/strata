<?php
namespace Strata\Core;

use Strata\Utility\Hash;

trait StrataConfigurableTrait
{

    protected $configuration = array();
    private $isConfigurableNormalized = false;

    /**
     * Fetches a value in the configuration array
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function getConfig($key)
    {
        return Hash::get($this->getConfiguration(), $key);
    }

    /**
     * Saves a value in the object's configuration array for the duration of the runtime.
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function setConfig($key, $value)
    {
        $this->configuration = Hash::merge($this->getConfiguration(), array($key => $value));
    }

    public function configure($config)
    {
        $this->configuration = $config;
    }

    /**
     * Confirms the presence of a value in the custom post type's configuration array
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function hasConfig($key)
    {
        return Hash::check($this->getConfiguration(), $key);
    }

    public function containsConfigurations()
    {
        return !is_null($this->configuration) && count($this->getConfiguration());
    }

    public function getConfiguration()
    {
        $this->normalizeConfiguration();
        return $this->configuration;
    }

    protected function normalizeConfiguration()
    {
        if (!$this->isConfigurableNormalized) {
            $this->configuration = Hash::normalize($this->configuration);
        }
    }
}
