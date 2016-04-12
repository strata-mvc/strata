<?php

namespace Strata\Test;

use Strata\Strata;
use PHPUnit_Framework_TestCase;

/**
 * A class to contain test cases and run them with shared fixtures
 */
class Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Strata::mockTestEnvironment();
    }

    public function testExtendsCorrectTestObject()
    {
        $this->assertTrue($this instanceof PHPUnit_Framework_TestCase);
    }
}
