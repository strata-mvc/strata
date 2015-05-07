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
        return $obj->wpPrefix . strtolower($obj->getShortName());
    }

    public $configuration     = array();

    function __construct()
    {
        $this->_normalizeConfiguration();
        parent::__construct();
    }

    private function _normalizeConfiguration()
    {
        $this->configuration = Hash::normalize($this->configuration);
    }

}
