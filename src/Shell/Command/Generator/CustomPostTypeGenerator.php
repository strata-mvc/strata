<?php
namespace Strata\Shell\Command\Generator;

class CustomPostTypeGenerator extends ModelGenerator
{
    protected function getExtends()
    {
        return "AppCustomPostType";
    }

    protected function getScaffoldMessage()
    {
        return "Scaffolding custom post type <info>{$this->classname}</info>";
    }
}
