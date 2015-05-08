<?php

use Tests\Fixtures\Strata\Strata;

class StrataTest extends PHPUnit_Framework_TestCase
{
    public function testCanBeInstanciated()
    {
        $this->assertTrue(new Strata(array()) instanceof Strata);
    }

    public function testCanBeConfigured()
    {
        $strata = new Strata();
        $strata->configure(array(
            "first_level" => array(
                "second_level" => true
            )
        ));

        $value = array_pop($strata->getConfig("first_level.second_level"));
        $this->assertTrue($value);
    }


}
