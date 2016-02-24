<?php

namespace Strata\Security;

/**
 * Base class for all the objects that secure or standardize the Wordpress installation.
 */
abstract class ImprovementBase
{
    /**
     * Registers the filter that handles comment validation if required.
     */
    abstract public function register();
}
