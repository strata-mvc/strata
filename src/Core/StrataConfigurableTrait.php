<?php

namespace Strata\Core;

use Strata\Utility\Hash;

/**
 * Enhances objects with a configuration cache. Gives the ability
 * to query and maintain a key => value hash that can be modified
 * and manipulated easily using dot notation formatting.
 */
trait StrataConfigurableTrait
{
    /**
     * The configuration cache.
     * @var array
     */
    protected $configuration = array();

    /**
     * Flag that confirms the configuration cache
     * has been normalized.
     * @var boolean
     */
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
     * Intelligently extract data from the
     * configuration array.
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function extractConfig($key)
    {
        return Hash::extract($this->getConfiguration(), $key);
    }

    /**
     * Saves a value in the object's configuration array for the duration of the runtime.
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function setConfig($key, $value)
    {
        $this->configuration = Hash::insert($this->getConfiguration(), $key, $value);
    }

    /**
     * Instantiate the configuration cache to the state supplied by $config.
     * @param  array $config
     */
    public function configure($config)
    {
        foreach ((array)$config as $key => $value) {
            $this->setConfig($key, $value);
        }
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

    /**
     * Returns whether the configuration cache contains something.
     * @return boolean
     */
    public function containsConfigurations()
    {
        return !is_null($this->configuration) && count($this->getConfiguration()) > 0;
    }

    /**
     * Returns the object's complete configuration cache.
     * @return array
     */
    public function getConfiguration()
    {
        $this->normalizeConfiguration();
        return (array)$this->configuration;
    }

    /**
     * Normalizes the configuration cache. This will only run once
     * on the object. It is mainly a safegard against a badly configured
     * value cache.
     * @return null
     */
    protected function normalizeConfiguration()
    {
        if (!$this->isConfigurableNormalized) {
            $this->configuration = Hash::normalize($this->configuration);
        }
    }
}
