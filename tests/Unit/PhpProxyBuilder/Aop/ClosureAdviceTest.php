<?php

namespace Tests\Unit\PhpProxyBuilder\Aop;

use PhpProxyBuilder\Aop\Advice\ClosureAdvice;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;

class ClosureAdviceTest extends \PHPUnit_Framework_TestCase {

    public function testReturnTypes() {
        $counter = 0;
        $name = "";
        $sampleClosure = function($joinPoint) use (&$counter, &$name) {
                    $counter++;
                    $name = $joinPoint->getMethodName();
                    return 567;
                };

        $joinPoint = $this->getMock('PhpProxyBuilder\Aop\ProceedingJoinPointInterface');
        $joinPoint->expects($this->never())->method("proceed");
        $joinPoint->expects($this->once())->method("getMethodName")->will($this->returnValue("methodx"));
        $instance = new ClosureAdvice($sampleClosure);

        $this->assertEquals(567, $instance->interceptMethodCall($joinPoint));
        $this->assertEquals(1, $counter);
        $this->assertEquals("methodx", $name);
    }

}