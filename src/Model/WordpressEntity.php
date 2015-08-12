<?php
namespace Strata\Model;

use Strata\Utility\Hash;
use Strata\Model\Model;

class WordpressEntity extends Model
{
    /**
     * Returns the internal custom post type slug
     */
    public static function wordpressKey()
    {
        $obj = self::staticFactory();
        return $obj->getWordpressKey();
    }

    public $configuration     = array();

    public $permissionLevel = 'edit_posts';

    function __construct()
    {
        $this->configuration = Hash::normalize($this->configuration);
    }

    /**
     * Fetches a value in the custom post type's configuration array
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function getConfig($key)
    {
        return Hash::get($this->configuration, $key);
    }

    /**
     * Confirms the presence of a value in the custom post type's configuration array
     * @param string $key In dot-notation format
     * @return mixed
     */
    public function hasConfig($key)
    {
        return Hash::check($this->configuration, $key);
    }

    public function getWordpressKey()
    {
        return $this->wpPrefix . strtolower($this->getShortName());
    }

}
