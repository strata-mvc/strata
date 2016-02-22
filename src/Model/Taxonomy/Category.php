<?php

namespace Strata\Model\Taxonomy;

/**
 *  Placeholder for post categories
 */
class Category extends Taxonomy
{
    /**
     * {@inheritdoc}
     */
    public function wordpressKey()
    {
        return "category";
    }
}
