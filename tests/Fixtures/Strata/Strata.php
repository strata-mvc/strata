<?php

namespace Tests\Fixtures\Strata;

use Strata\Strata as StrataBase;

class Strata extends StrataBase {

    function __construct()
    {
        $this->reset();
        $this->_ready = true;
    }

    public function reset()
    {
        $this->_config = array(
            "namespace" => "Tests\Fixtures"
        );
    }

    public function configure($config)
    {
        $this->_saveConfigValues($config);
    }

}
