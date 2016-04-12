<?php

namespace Strata\Test;

use Strata\Strata;
use PHPUnit_Framework_TestCase;

/**
 * A class to contain test cases and run them with shared fixtures
 */
class Test extends PHPUnit_Framework_TestCase
{
    private $instance;

    public function setUp()
    {
        if (is_null($GLOBALS)) {
            $GLOBALS = array();
        }

        if (!array_key_exists('_SESSION', $GLOBALS)) {
            $GLOBALS['_SESSION'] = array();
        }

        if (!array_key_exists('_SERVER', $GLOBALS)) {
            $GLOBALS['_SERVER'] = array();
        }

        $GLOBALS['_SERVER']['SERVER_PROTOCOL'] = "http";
        $GLOBALS['_SERVER']['REQUEST_METHOD'] = "GET";
        $GLOBALS['_SERVER']['REQUEST_URI'] = getenv("WP_HOME");

        if (!defined('WP_USE_THEMES')) {
            define('WP_USE_THEMES', false);
        }

        $this->instance = Strata::bootstrap(Strata::requireVendorAutoload());
        if (!defined('ABSPATH')) {
            require(Strata::getRootPath() . '/web/wp/wp-load.php');
        }
        $this->instance->run();
    }

    public function testExtendsCorrectTestObject()
    {
        $this->assertTrue($this instanceof PHPUnit_Framework_TestCase);
    }

    protected function app()
    {
        return $this->instance;
    }
}
