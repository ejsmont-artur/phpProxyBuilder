<?php

namespace Tests\Unit\PhpProxyBuilder\Proxy;

use PhpProxyBuilder\Adapter\Cache\SimpleArrayCache;
use PhpProxyBuilder\Aop\Advice\NullAdvice;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;
use PhpProxyBuilder\Factory\MagicMethodBasedFactory;
use PhpProxyBuilder\Proxy\MagicMethodProxy;
use PhpProxyBuilder\Example\ExampleService;

class MagicMethodBasedFactoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ExampleService
     */
    private $target;

    /**
     * @var NullAdvice
     */
    private $advice;

    /**
     * @var MagicMethodBasedFactory
     */
    private $factory;

    public function setup() {
        parent::setup();
        $this->factory = new MagicMethodBasedFactory();
        $this->advice = new NullAdvice();
        $this->target = new ExampleService();
    }

    // ===================================================== TESTS ====================================================

    public function testNonCachedCall() {
        $result = $this->factory->createProxy($this->advice, $this->target);

        $this->assertTrue($result instanceof MagicMethodProxy);
    }

    public function testSelectiveMethods() {
        // some proxied some not
        $this->markTestIncomplete();
    }

    public function testAllMethods() {
        // some proxied some not
        $this->markTestIncomplete();
    }

    public function testSingleMethod() {
        // some proxied some not
        $this->markTestIncomplete();
    }

    public function testSingleGetter() {
        // some proxied some not
        $this->markTestIncomplete();
    }

    public function testSingleMagicGetter() {
        // some proxied some not
        $this->markTestIncomplete();
    }

    public function testToString() {
        // some proxied some not
        $this->markTestIncomplete();
    }

}