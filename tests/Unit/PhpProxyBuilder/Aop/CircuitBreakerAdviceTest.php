<?php

namespace Tests\Unit\PhpProxyBuilder\Aop;

use PhpProxyBuilder\Aop\Advice\CircuitBreakerAdvice;
use PhpProxyBuilder\Adapter\CircuitBreaker;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;
use Exception;

/**
 * Service available:
 *                                      A               B                   C
 *                                   success,   throws not on list,    throws on list
 *   1  all defaults                    +               +                  N/A
 *   2  empty list + no $throw          +              N/A                 N/A
 *   3  empty list + $throw             +              N/A                 N/A
 *   4  list + no $throw                +               +                   +
 *   5  list + $throw                   +               +                   +
 * 
 * Service unavailable:
 *  U1  no $throw (same as default)
 *  U2  $throw
 * 
 */
class CircuitBreakerAdviceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|CircuitBreaker
     */
    private $breaker;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|ProceedingJoinPointInterface
     */
    private $joinPoint;

    /**
     * @var CircuitBreakerAdvice
     */
    private $advice;

    public function setup() {
        $this->breaker = $this->getMock('PhpProxyBuilder\Adapter\CircuitBreaker');
        $this->joinPoint = $this->getMock('PhpProxyBuilder\Aop\ProceedingJoinPointInterface');
        $this->advice = new CircuitBreakerAdvice($this->breaker, "fakeService");
    }

    // ================================================ TESTS =========================================================

    public function testSuccessDefaults() {
        // A1 - successful calls on default settings
        $this->breaker
                ->expects($this->exactly(3))
                ->method("isAvailable")
                ->will($this->onConsecutiveCalls(true, true, true));
        $this->joinPoint
                ->expects($this->exactly(3))
                ->method("proceed")
                ->will($this->onConsecutiveCalls("a1", "a2", "a3"));
        $this->breaker->expects($this->exactly(3))->method("reportSuccess");
        $this->assertEquals("a1", $this->advice->interceptMethodCall($this->joinPoint));
        $this->assertEquals("a2", $this->advice->interceptMethodCall($this->joinPoint));
        $this->assertEquals("a3", $this->advice->interceptMethodCall($this->joinPoint));
    }

    /**
     * @dataProvider successProvider
     */
    public function testSuccessProvided($exceptionNames, $throw) {
        // A2, A3, A4, A5 - successful calls with different combos 
        $this->advice = new CircuitBreakerAdvice($this->breaker, "fakeService", $exceptionNames, $throw);
        $this->breaker
                ->expects($this->exactly(3))
                ->method("isAvailable")
                ->will($this->onConsecutiveCalls(true, true, true));
        $this->joinPoint
                ->expects($this->exactly(3))
                ->method("proceed")
                ->will($this->onConsecutiveCalls("a1", "a2", "a3"));
        $this->breaker->expects($this->exactly(3))->method("reportSuccess");
        $this->assertEquals("a1", $this->advice->interceptMethodCall($this->joinPoint));
        $this->assertEquals("a2", $this->advice->interceptMethodCall($this->joinPoint));
        $this->assertEquals("a3", $this->advice->interceptMethodCall($this->joinPoint));
    }

    public function successProvider() {
        return array(
            array(array(), new \InvalidArgumentException("set1")),
            array(array("\InvalidArgumentException"), new \InvalidArgumentException("set1")),
            array(array("\InvalidArgumentException"), null),
            array(array("\ErrorException"), new \InvalidArgumentException("set1")),
            array(array("\Exception"), new \InvalidArgumentException("set1")),
        );
    }

    /**
     * @expectedException \ErrorException
     */
    public function testFailDefault() {
        // B1 - fails with some exception on default settings
        $this->breaker->expects($this->once())->method("isAvailable")->will($this->returnValue(true));
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->throwException(new \ErrorException()));
        $this->breaker->expects($this->once())->method("reportFailure");
        $this->advice->interceptMethodCall($this->joinPoint);
    }

    /**
     * @expectedException \ErrorException
     */
    public function testFailNotOnList() {
        // B4 - fails with exception that was not on the list so it was code error or user error, reports success
        $this->advice = new CircuitBreakerAdvice($this->breaker, "fakeService", array('\InvalidArgumentException'));
        $this->breaker->expects($this->once())->method("isAvailable")->will($this->returnValue(true));
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->throwException(new \ErrorException()));
        $this->breaker->expects($this->once())->method("reportSuccess");
        $this->advice->interceptMethodCall($this->joinPoint);
    }

    /**
     * @expectedException \ErrorException
     */
    public function testFailNotOnListSecond() {
        // B5 - same as B4
        $this->advice = new CircuitBreakerAdvice($this->breaker, "fakeService", array('\InvalidArgumentException'), new \Exception());
        $this->breaker->expects($this->once())->method("isAvailable")->will($this->returnValue(true));
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->throwException(new \ErrorException()));
        $this->breaker->expects($this->once())->method("reportSuccess");
        $this->advice->interceptMethodCall($this->joinPoint);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailOnList() {
        // C4 - fails with exception that was on the list, so it was service unavailability, reports failure
        $this->advice = new CircuitBreakerAdvice($this->breaker, "fakeService", array('\InvalidArgumentException'));
        $this->breaker->expects($this->once())->method("isAvailable")->will($this->returnValue(true));
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->throwException(new \InvalidArgumentException()));
        $this->breaker->expects($this->once())->method("reportFailure");
        $this->advice->interceptMethodCall($this->joinPoint);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailOnListSecond() {
        // C5 - same as C4
        $this->advice = new CircuitBreakerAdvice($this->breaker, "fakeService", array('\InvalidArgumentException'), new \ErrorException());
        $this->breaker->expects($this->once())->method("isAvailable")->will($this->returnValue(true));
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->throwException(new \InvalidArgumentException()));
        $this->breaker->expects($this->once())->method("reportFailure");
        $this->advice->interceptMethodCall($this->joinPoint);
    }

    /**
     * @expectedException PhpProxyBuilder\Adapter\CircuitBreaker\ServiceUnavailableException
     */
    public function testRejectDefault() {
        // U1 - circuit breaker thinks that service is unavailable, throws default exception
        $this->advice = new CircuitBreakerAdvice($this->breaker, "fakeService");
        $this->breaker->expects($this->once())->method("isAvailable")->will($this->returnValue(false));
        $this->joinPoint->expects($this->never())->method("proceed");
        $this->breaker->expects($this->never())->method("reportFailure");
        $this->breaker->expects($this->never())->method("reportSuccess");
        $this->advice->interceptMethodCall($this->joinPoint);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRejectCustom() {
        // U2 - circuit breaker thinks that service is unavailable, throws exception provided in constructor
        $this->advice = new CircuitBreakerAdvice($this->breaker, "fakeService", array(), new \InvalidArgumentException());
        $this->breaker->expects($this->once())->method("isAvailable")->will($this->returnValue(false));
        $this->joinPoint->expects($this->never())->method("proceed");
        $this->breaker->expects($this->never())->method("reportFailure");
        $this->breaker->expects($this->never())->method("reportSuccess");
        $this->advice->interceptMethodCall($this->joinPoint);
    }

}