<?php
namespace Strata\Model;

use Strata\Utility\Inflector;
use Strata\Core\StrataConfigurableTrait;

class WordpressEntity extends Model
{
    use StrataConfigurableTrait;

    /**
     * Returns the internal custom post type slug
     */
    public static function wordpressKey()
    {
        $obj = self::staticFactory();
        return $obj->getWordpressKey();
    }

    public $permissionLevel = 'edit_posts';

    public function getWordpressKey()
    {
        $name = $this->getShortName();
        $name = Inflector::underscore($name);

        return $this->wpPrefix . $name;
    }
}
