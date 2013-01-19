<?php

namespace Tests\Unit\PhpProxyBuilder\Aop;

use PhpProxyBuilder\Aop\Advice\LoggingAdvice;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;
use PhpProxyBuilder\Adapter\Log\DummyArrayLog;
use Exception;

class LoggingAdviceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ProceedingJoinPointInterface|PHPUnit_Framework_MockObject_MockObject 
     */
    private $joinPoint;

    /**
     * @var DummyArrayLog
     */
    private $logger;

    public function setup() {
        $this->joinPoint = $this->getMock('PhpProxyBuilder\Aop\ProceedingJoinPointInterface');
        $this->logger = new DummyArrayLog();
    }

//    /**
//     * @expectedException \InvalidArgumentException
//     */
//    public function testExceptionLog() {
//        $this->joinPoint->expects($this->once())->method("proceed")->will($this->throwException(new \InvalidArgumentException("E1")));
//        $instance = new LoggingAdvice($this->logger, "someName");
//        $instance->interceptMethodCall($this->joinPoint);
//    }

    public function testBoth() {
        $this->joinPoint->expects($this->once())->method("getMethodName")->will($this->returnValue("M1"));
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->returnValue("R1"));
        $this->joinPoint->expects($this->once())->method("getArguments")->will($this->returnValue("ARG1"));
        $instance = new LoggingAdvice($this->logger, "logName1", LoggingAdvice::LOG_ARGUMENTS_AND_RETURN);
        $result = $instance->interceptMethodCall($this->joinPoint);

        $logged = $this->logger->getMessages();

        $this->assertEquals("R1", $result);
        $this->assertEquals("debug: Proxy logName1::M1 ... ARG1", $logged[0]);
        $this->assertRegExp('/^debug: Proxy logName1::M1 returned in.*R1/msi', $logged[1]);
    }

    public function testArgs() {
        $this->joinPoint->expects($this->once())->method("getMethodName")->will($this->returnValue("M2"));
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->returnValue("R2"));
        $this->joinPoint->expects($this->once())->method("getArguments")->will($this->returnValue("ARG2"));
        $instance = new LoggingAdvice($this->logger, "logName1", LoggingAdvice::LOG_ARGUMENTS);
        $result = $instance->interceptMethodCall($this->joinPoint);

        $logged = $this->logger->getMessages();

        $this->assertEquals("R2", $result);
        $this->assertEquals("debug: Proxy logName1::M2 ... ARG2", $logged[0]);
        $this->assertRegExp('/^debug: Proxy logName1::M2 returned in [0-9.]+; /msi', $logged[1]);
    }

    public function testResult() {
        $this->joinPoint->expects($this->once())->method("getMethodName")->will($this->returnValue("M3"));
        $this->joinPoint->expects($this->once())->method("proceed")->will($this->returnValue("R3"));
        $this->joinPoint->expects($this->never())->method("getArguments");
        $instance = new LoggingAdvice($this->logger, "logName1", LoggingAdvice::LOG_RESULT);
        $result = $instance->interceptMethodCall($this->joinPoint);

        $logged = $this->logger->getMessages();

        $this->assertEquals("R3", $result);
        $this->assertEquals("debug: Proxy logName1::M3 ... ", $logged[0]);
        $this->assertRegExp('/^debug: Proxy logName1::M3 returned in [0-9.]+; /msi', $logged[1]);
    }

}