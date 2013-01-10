<?php

namespace Tests\Unit\PhpProxyBuilder\Adapter\Log;

use PhpProxyBuilder\Adapter\CircuitBreaker\ServiceUnavailableException;
use PhpProxyBuilder\PhpProxyException;

class RemainingInterfacesTest extends \PHPUnit_Framework_TestCase {

    public function testSanity() {
        $this->assertTrue(interface_exists('PhpProxyBuilder\Adapter\InstrumentationMonitor'));
        $this->assertTrue(interface_exists('PhpProxyBuilder\Adapter\CircuitBreaker'));
    }

    public function testSanityExceptions() {
        $e = new ServiceUnavailableException();
        $this->assertTrue($e instanceof PhpProxyException);
    }

}