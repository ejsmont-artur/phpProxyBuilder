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

    // ============================================ TESTS =============================================================

    /**
     * Test if regular method calls are proxied 
     */
    public function testDirectRandomCall() {
        $res1 = $this->target->getRandomValue("ab", "xy");
        $res2 = $this->target->getRandomValue("ab", "xy");
        $this->assertTrue($res1 != $res2);
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res1) > 0);
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res2) > 0);
    }

    /**
     * Test if regular method calls are proxied 
     */
    public function testProxiedRandomCall() {
        $res1 = $this->proxy->getRandomValue("ab", "xy");
        $res2 = $this->proxy->getRandomValue("ab", "xy");
        $this->assertTrue($res1 == $res2, "got: $res1");
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res1) > 0, "got: $res1");
    }

    /**
     * Test if method calls are proxies via proxy or not when selectively proxying based on method name
     */
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

    /**
     * Test if you can get properties of the target via proxy using magic __get method
     */
    public function testMagicGet() {
        $this->target->someState = "magicSet-1";
        $this->assertEquals("magicSet-1", $this->proxy->someState);
        $this->assertEquals("magicSet-1", $this->target->someState);
    }

    /**
     * Test if you can set and get properties on the target via proxy
     */
    public function testMagicGetSet() {
        $this->proxy->someState = "magicSet-2";
        $this->assertEquals("magicSet-2", $this->proxy->someState);
        $this->assertEquals("magicSet-2", $this->target->someState);
    }

    /**
     * Test if you can set and get properties on the target via proxy
     */
    public function testMagicGetSetNonExisting() {
        $this->proxy->fakeProperty = "magicSet-3";
        $this->assertEquals("magicSet-3", $this->proxy->fakeProperty);
        $this->assertEquals("magicSet-3", $this->target->fakeProperty);
    }

    /**
     * Test if it is illegal to call protected methods on the target
     * 
     * @expectedException \Exception
     */
    public function testProtectedMethodsNotAllowed() {
        $this->proxy->notAllowed();
    }

    /**
     * Test if same method call would work if it was public
     */
    public function testPublicMethodsAllowed() {
        $this->target = new ExampleServiceMock();
        $this->proxy = new MagicMethodProxy($this->advice, $this->target);

        $this->assertTrue($this->proxy->notAllowed() > 0);
    }

    /**
     * Test if you can proxy __toString magic method calls
     */
    public function testToString() {
        $res = (string) $this->proxy;
        $this->assertTrue(preg_match('/^[0-9]+\.[0-9]+$/', $res) > 0);
    }

    /**
     * Test if you can proxy __sleep magic method calls
     */
    public function testSleep() {
        $this->assertEquals(array("slept fine"), $this->proxy->__sleep());
    }

    /**
     * Test if you can proxy __wakeup magic method calls
     */
    public function testWakeUp() {
        $this->target->setWakeupCount(0);
        $this->proxy->__wakeup();
        $this->assertEquals(1, $this->target->getWakeupCount());
    }

    /**
     * We test destructor via garbage collector. We dont want to call __destruct on the target.
     * There could be other references to target instance so we just destroy the proxy and joinPoint
     * and their references to target but we keep target object untouched. 
     * 
     * local reference to target is unset so when ew call gc_collect_cycles it should kill proxie's destructor
     * which will unset join point references and its own.
     * 
     */
    public function testDestruct() {
        $this->target->setDestructCount(0);
        unset($this->target);
        unset($this->proxy);

        gc_collect_cycles();

        $this->target = new ExampleService();
        $this->assertEquals(1, $this->target->getDestructCount());
    }

}

class ExampleServiceMock extends ExampleService {

    /**
     * Example of protected method - added just for tests, service does not need that!
     * 
     * @return int 
     */
    public function notAllowed() {
        return parent::notAllowed();
    }

}