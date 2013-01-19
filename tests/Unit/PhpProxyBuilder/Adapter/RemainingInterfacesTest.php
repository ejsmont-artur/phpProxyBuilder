<?php

namespace Tests\Unit\PhpProxyBuilder\Adapter\Log;

use PhpProxyBuilder\Adapter\CircuitBreaker\ServiceUnavailableException;
use PhpProxyBuilder\Adapter\InstrumentorInterface;
use PhpProxyBuilder\PhpProxyException;

class RemainingInterfacesTest extends \PHPUnit_Framework_TestCase {

    public function testSanity() {
        $this->assertTrue(interface_exists('PhpProxyBuilder\Adapter\InstrumentorInterface'));
        $this->assertTrue(interface_exists('PhpProxyBuilder\Adapter\CircuitBreakerInterface'));
    }

    public function testSanityExceptions() {
        $e = new ServiceUnavailableException();
        $this->assertTrue($e instanceof PhpProxyException);
    }

}