<?php

namespace Tests\Unit\PhpProxyBuilder\Adapter\Log;

use PhpProxyBuilder\Aop\Advice\NullAdvice;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;

class NullAdviceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider dataSamplesProvider
     */
    public function testReturnTypes($value) {
        $joinPoint = $this->getMock('PhpProxyBuilder\Aop\ProceedingJoinPointInterface');
        $joinPoint->expects($this->once())->method("proceed")->will($this->returnValue($value));
        $instance = new NullAdvice();
        $this->assertEquals($value, $instance->interceptMethodCall($joinPoint));
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

}