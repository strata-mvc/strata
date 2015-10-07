<?php
namespace Strata\Model;

use Strata\Core\StrataObjectTrait;
use Strata\Model\CustomPostType\ModelEntity;

/**
 * A base class for model objects
 */
class Model
{

    use StrataObjectTrait;

    public static function getNamespaceStringInStrata()
    {
        return "Model";
    }

    public static function getClassNameSuffix()
    {
        return "";
    }

    public static function getEntity($associatedObj = null)
    {
        $EntityClass = get_called_class();
        $entityClassRef = new $EntityClass();
        $ActualEntity = ModelEntity::generateClassPath($entityClassRef->getShortName());
        return class_exists($ActualEntity) ? new $ActualEntity($associatedObj) : new ModelEntity($associatedObj);
    }

    public function __construct()
    {

    }
}
