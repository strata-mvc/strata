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
        if (!defined('STRATA_INCLUDED_WORDPRESS')) {
            $this->includeWordpress();
        }
    }

    public function testExtendsCorrectTestObject()
    {
        $this->assertTrue($this instanceof PHPUnit_Framework_TestCase);
    }

    private function includeWordpress()
    {
        define('STRATA_INCLUDED_WORDPRESS', true);

        if (!defined('WP_USE_THEMES')) {
            define('WP_USE_THEMES', false);
        }

        $GLOBALS['_SERVER']['SERVER_PROTOCOL'] = "http";
        $GLOBALS['_SERVER']['REQUEST_METHOD'] = "GET";

        require_once Strata::getVendorPath() . DIRECTORY_SEPARATOR . 'autoload.php';

        ob_start();
        require_once Strata::getWordpressPath() . DIRECTORY_SEPARATOR . 'wp-blog-header.php';
        $output = ob_get_clean();

        // @todo: Validate common errors from the output and carry them over to the test suite:
        //  DB connection + fatal errors

        $projectBootstrapFile = Strata::getTestPath() . DIRECTORY_SEPARATOR . 'strata-test-bootstraper.php';
        if (file_exists($projectBootstrapFile)) {
            require_once($projectBootstrapFile);
        }
    }
}
