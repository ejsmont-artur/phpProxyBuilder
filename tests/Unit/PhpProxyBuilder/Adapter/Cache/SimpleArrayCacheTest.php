<?php

namespace Tests\Unit\PhpProxyBuilder\Adapter\Cache;

use PhpProxyBuilder\Adapter\Cache\SimpleArrayCache;
use PhpProxyBuilder\Adapter\InstrumentorInterface;


class SimpleArrayCacheTest extends \PHPUnit_Framework_TestCase {

    public function testSanity() {
        $this->assertTrue(interface_exists('PhpProxyBuilder\Adapter\InstrumentorInterface'));
        $this->assertTrue(interface_exists('PhpProxyBuilder\Adapter\CircuitBreakerInterface'));
    }

    public function testSanityExceptions() {
        $cache = new SimpleArrayCache(2);

        $cache->set("1", "a1");
        $cache->set("2", "a2");
        $cache->set("3", "a3");
        $cache->set("4", "a4");

        $this->assertEquals(null, $cache->get("1"));
        $this->assertEquals(null, $cache->get("2"));
        $this->assertEquals("a3", $cache->get("3"));
        $this->assertEquals("a4", $cache->get("4"));
    }

}