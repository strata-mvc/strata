<?php

namespace Strata\Shell\Command\Generator;

/**
 * Generates a Strata custom post type
 */
class CustomPostTypeGenerator extends ModelGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function getExtends()
    {
        return "AppCustomPostType";
    }

    /**
     * {@inheritdoc}
     */
    protected function getScaffoldMessage()
    {
        return "Scaffolding custom post type <info>{$this->classname}</info>";
    }
}
