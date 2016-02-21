<?php

namespace Strata\Model\CustomPostType\Registrar;

use Strata\Model\CustomPostType\CustomPostType;

/**
 * A custom post type registrar. Based on a model entity,
 * it attempts to automate the configuration and registration
 * of custom post types.
 */
class Registrar
{
    /**
     * @var CustomPostType A custom post type Model
     */
    protected $model;

    /**
     * The registrar must be associated to a Strata model.
     * @param CustomPostType $model
     */
    function __construct(CustomPostType $model)
    {
        $this->model = $model;
    }
}
