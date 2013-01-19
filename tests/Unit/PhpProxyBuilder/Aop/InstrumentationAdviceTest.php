<?php

namespace Tests\Unit\PhpProxyBuilder\Aop;

use PhpProxyBuilder\Aop\Advice\InstrumentationAdvice;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;
use PhpProxyBuilder\Adapter\Intrumentor\SimpleArrayIntrumentor;
use Exception;

class InstrumentationAdviceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ProceedingJoinPointInterface|PHPUnit_Framework_MockObject_MockObject 
     */
    private $joinPoint;

    /**
     * @var InstrumentationAdvice
     */
    private $advice;

    /**
     * @var SimpleArrayIntrumentor
     */
    private $instrumentor;

    public function setup() {
        $this->joinPoint = $this->getMock('PhpProxyBuilder\Aop\ProceedingJoinPointInterface');
        $this->instrumentor = new SimpleArrayIntrumentor();
        $this->advice = new InstrumentationAdvice($this->instrumentor, "testService", false);
    }

    public function testEmpty() {
        $this->assertEquals(0, $this->instrumentor->getCounter("fake"));
        $this->assertEquals(0, $this->instrumentor->getCounter("testService.success"));
        $this->assertEquals(0, $this->instrumentor->getTimer("testService.success"));
        $this->assertEquals(0, $this->instrumentor->getTimer("fake"));
    }

    public function testCounterPerMethodSuccess() {
        $this->advice = new InstrumentationAdvice($this->instrumentor, "testService", true);

        $this->joinPoint->expects($this->exactly(2))->method("getMethodName")->will($this->returnValue("M1"));
        $this->joinPoint->expects($this->exactly(2))->method("proceed")->will($this->returnValue("R1"));

        $result = $this->advice->interceptMethodCall($this->joinPoint);
        $result = $this->advice->interceptMethodCall($this->joinPoint);

        $this->assertEquals("R1", $result);
        $this->assertEquals(2, $this->instrumentor->getCounter("testService.M1.success"));
        $this->assertTrue($this->instrumentor->getTimer("testService.M1.success") > 0);
    }

    public function testCounterAggregateSuccess() {
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->returnValue("R1"));

        $result = $this->advice->interceptMethodCall($this->joinPoint);

        $this->assertEquals("R1", $result);
        $this->assertEquals(1, $this->instrumentor->getCounter("testService.success"));
        $this->assertTrue($this->instrumentor->getTimer("testService.success") > 0);
    }

    public function testCounterAggregateDefault() {
        $this->advice = new InstrumentationAdvice($this->instrumentor);

        $target = new \stdClass();
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->returnValue("R1"));
        $this->joinPoint->expects($this->once())->method("getTarget")->will($this->returnValue($target));

        $result = $this->advice->interceptMethodCall($this->joinPoint);

        $this->assertEquals("R1", $result);
        $this->assertEquals(1, $this->instrumentor->getCounter("stdClass.success"));
        $this->assertTrue($this->instrumentor->getTimer("stdClass.success") > 0);
    }

    public function testCounterAggregateDefaultError() {
        $this->joinPoint
                ->expects($this->once())
                ->method("proceed")
                ->will($this->throwException(new \InvalidArgumentException()));

        try {
            $result = $this->advice->interceptMethodCall($this->joinPoint);
            $exception = false;
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }

        $this->assertEquals(true, $exception);
        $this->assertEquals(1, $this->instrumentor->getCounter("testService.error"));
        $this->assertTrue($this->instrumentor->getTimer("testService.error") > 0);
        $this->assertEquals(0, $this->instrumentor->getCounter("testService.success"));
        $this->assertTrue($this->instrumentor->getTimer("testService.success") == 0);
    }

}