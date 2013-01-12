<?php

namespace Tests\Unit\PhpProxyBuilder\Proxy;

use PhpProxyBuilder\Aop\JoinPoint\DynamicJoinPoint;
use PhpProxyBuilder\Aop\Advice\CachingAdvice;
use PhpProxyBuilder\Example\ExampleService;
use PhpProxyBuilder\Proxy\MagicMethodProxy;
use PhpProxyBuilder\Adapter\Cache\SimpleArrayCache;

class MagicMethodProxyTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var MagicMethodProxy 
     */
    private $proxy;

    /**
     * @var ExampleService
     */
    private $target;

    /**
     * @var SimpleArrayCache
     */
    private $advice;

    public function setup() {
        parent::setup();

        $cache = new SimpleArrayCache(100);
        $this->advice = new CachingAdvice($cache);
        $this->target = new ExampleService();
        $this->proxy = new MagicMethodProxy($this->advice, $this->target);
    }

    public function testDirectRandomCall() {
        $res1 = $this->target->getRandomValue("ab", "xy");
        $res2 = $this->target->getRandomValue("ab", "xy");
        $this->assertTrue($res1 != $res2);
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res1) > 0);
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res2) > 0);
    }

    public function testProxiedRandomCall() {
        $res1 = $this->proxy->getRandomValue("ab", "xy");
        $res2 = $this->proxy->getRandomValue("ab", "xy");
        $this->assertTrue($res1 == $res2, "got: $res1");
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res1) > 0, "got: $res1");
    }

    public function testSelectivelyNotProxiedRandomCall() {
        // we cache other method calls but not get random
        $this->proxy = new MagicMethodProxy($this->advice, $this->target, array('getOtherValue'));

        $res1 = $this->proxy->getRandomValue("ab", "xy");
        $res2 = $this->proxy->getRandomValue("ab", "xy");

        $this->assertTrue($res1 != $res2);
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res1) > 0);
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res2) > 0);

        $res3 = $this->proxy->getOtherValue();
        $res4 = $this->proxy->getOtherValue();

        $this->assertTrue($res3 === $res3);
        $this->assertTrue(preg_match('/^[0-9]+$/', $res3) > 0);
        $this->assertTrue(preg_match('/^[0-9]+$/', $res4) > 0);
    }

}