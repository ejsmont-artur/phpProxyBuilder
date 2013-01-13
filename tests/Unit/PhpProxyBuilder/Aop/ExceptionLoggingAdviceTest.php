<?php

namespace Tests\Unit\PhpProxyBuilder\Aop;

use PhpProxyBuilder\Aop\Advice\ExceptionLoggingAdvice;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;
use PhpProxyBuilder\Adapter\Log\DummyArrayLog;
use Exception;

class ExceptionLoggingAdviceTest extends \PHPUnit_Framework_TestCase {

    private $joinPoint;
    private $logger;

    public function setup() {
        $this->joinPoint = $this->getMock('PhpProxyBuilder\Aop\ProceedingJoinPointInterface');
        $this->logger = $this->getMock('PhpProxyBuilder\Adapter\Log');
    }

    /**
     * @dataProvider dataSamplesProvider
     */
    public function testReturnTypesNoException($value) {
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->returnValue($value));
        $this->logger->expects($this->never())->method("logWarning");

        $instance = new ExceptionLoggingAdvice($this->logger);
        $this->assertEquals($value, $instance->interceptMethodCall($this->joinPoint));
    }

    public function dataSamplesProvider() {
        return array(
            array(-123),
            array(0),
            array(false),
            array(null),
            array(true),
            array(456),
            array("allok"),
            array(array(45, 88, "asd")),
            array(array()),
        );
    }

    public function testExceptionLog() {
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->throwException(new \InvalidArgumentException("YO456")));
        $this->logger = new DummyArrayLog();
        $instance = new ExceptionLoggingAdvice($this->logger, 4);
        try {
            $instance->interceptMethodCall($this->joinPoint);
            $this->assertTrue(false, "we should never get here");
        } catch (Exception $e) {
            $this->assertEquals(1, $this->logger->getMessagesCount());
            $this->assertRegExp('/warning:.*ExceptionLoggingAdviceTest.*YO456.*trace truncated/msi', $this->logger->getLastMessage());
        }
    }

}