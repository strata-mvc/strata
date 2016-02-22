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

    /**
     * {@inheritdoc}
     */
    public static function getNamespaceStringInStrata()
    {
        return "Model";
    }

    /**
     * {@inheritdoc}
     */
    public static function getClassNameSuffix()
    {
        return "";
    }

    /**
     * Returns a ModelEntity related to this object.
     * @var mixed (Optional) Usually a WP_Post object, but can also be anything inheriting stdClass
     * @return ModelEntity
     */
    public static function getEntity($associatedObj = null)
    {
        $EntityClass = get_called_class();
        $entityClassRef = new $EntityClass();
        $ActualEntity = ModelEntity::generateClassPath($entityClassRef->getShortName());

        $entityRef = class_exists($ActualEntity) ? new $ActualEntity() : new ModelEntity();
        $entityRef->bindToObject($associatedObj);
        return $entityRef;
    }
}
