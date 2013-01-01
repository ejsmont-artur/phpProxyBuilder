<?php

namespace Tests\Unit\PhpProxyBuilder\Proxy;

use PhpProxyBuilder\Proxy\ArrayCachingProxy;
use PhpProxyBuilder\Aop\ProceedingJoinPoint;

class ArrayCachingProxyTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ArrayCachingProxy 
     */
    private $instance;

    /**
     * @var StdClass
     */
    private $target;

    /**
     * @var ProceedingJoinPoint|PHPUnit_Framework_MockObject_MockObject
     */
    private $jointPoint;

    public function setup() {
        parent::setup();

        $this->instance = new ArrayCachingProxy(2);
        $this->target = new \StdClass();

        $this->jointPoint = $this->getMock('PhpProxyBuilder\Aop\ProceedingJoinPoint');
        $this->jointPoint->expects($this->any())->method("getTarget")->will($this->returnValue($this->target));
    }

    public function testNonCachedCall() {
        $this->jointPoint->expects($this->exactly(2))->method("getMethodName")->will($this->returnValue("double"));
        $this->jointPoint->expects($this->any())
                ->method("getArgs")
                ->will($this->onConsecutiveCalls(array(2), array(3)));
        $this->jointPoint->expects($this->exactly(2))->method("proceed")->will($this->onConsecutiveCalls(4, 6));

        $this->assertEquals(4, $this->instance->interceptMethodCall($this->jointPoint));
        $this->assertEquals(6, $this->instance->interceptMethodCall($this->jointPoint));
    }

    // calling different method generates different cache key
    public function testNonCachedCallByMethodName() {
        $this->jointPoint->expects($this->exactly(2))->method("getMethodName")->will($this->onConsecutiveCalls("double", "add"));
        $this->jointPoint->expects($this->any())
                ->method("getArgs")
                ->will($this->onConsecutiveCalls(array(2), array(2)));
        $this->jointPoint->expects($this->exactly(2))->method("proceed")->will($this->onConsecutiveCalls(4, 2));

        $this->assertEquals(4, $this->instance->interceptMethodCall($this->jointPoint));
        $this->assertEquals(2, $this->instance->interceptMethodCall($this->jointPoint));
    }

    // even that we cann it 3 times jointPoint is called once
    public function testCachedCall() {
        $this->jointPoint->expects($this->exactly(3))->method("getMethodName")->will($this->returnValue("double"));
        $this->jointPoint->expects($this->any())
                ->method("getArgs")
                ->will($this->onConsecutiveCalls(array(2), array(2), array(2)));
        $this->jointPoint->expects($this->exactly(1))->method("proceed")->will($this->returnValue(4));

        $this->assertEquals(4, $this->instance->interceptMethodCall($this->jointPoint));
        $this->assertEquals(4, $this->instance->interceptMethodCall($this->jointPoint));
        $this->assertEquals(4, $this->instance->interceptMethodCall($this->jointPoint));
    }

    // reach capacity and then call with old args
    public function testCapacity() {
        $this->jointPoint->expects($this->exactly(5))->method("getMethodName")->will($this->returnValue("double"));
        $this->jointPoint->expects($this->any())
                ->method("getArgs")
                ->will($this->onConsecutiveCalls(array(1), array(2), array(3), array(4), array(1)));
        $this->jointPoint->expects($this->exactly(5))->method("proceed")->will($this->onConsecutiveCalls(2, 4, 6, 8, 2));

        $this->assertEquals(2, $this->instance->interceptMethodCall($this->jointPoint));
        $this->assertEquals(4, $this->instance->interceptMethodCall($this->jointPoint));
        $this->assertEquals(6, $this->instance->interceptMethodCall($this->jointPoint));
        $this->assertEquals(8, $this->instance->interceptMethodCall($this->jointPoint));
        $this->assertEquals(2, $this->instance->interceptMethodCall($this->jointPoint));
    }

}