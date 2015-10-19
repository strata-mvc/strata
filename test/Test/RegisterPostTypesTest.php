<?php

use Strata\Model\CustomPostType\CustomPostTypeLoader;

class RegisterPostTypesTest extends PHPUnit_Framework_TestCase
{
    public function testCanBeInstanciated()
    {
        $this->assertTrue(new CustomPostTypeLoader() instanceof CustomPostTypeLoader);
    }

    /**
     * @expectedException        Exception
     */
    public function testAddInvalidCustomPostType()
    {
        $loader = new CustomPostTypeLoader();
        $loader->configure(array(
            "I_Dont_Exist"
        ));
        $loader->load();
    }
}
