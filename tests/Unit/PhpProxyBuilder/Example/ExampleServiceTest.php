<?php

namespace Tests\Unit\PhpProxyBuilder\Example;

use PhpProxyBuilder\Example\ExampleService;

class ExampleServiceTest extends \PHPUnit_Framework_TestCase {

    public function testRandomCall() {
        $target = new ExampleService();

        $res1 = $target->getRandomValue("ab", "xy");
        $res2 = $target->getRandomValue("ab", "xy");

        $this->assertTrue($res1 != $res2);
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res1) > 0);
        $this->assertTrue(preg_match('/^ab[0-9]+xy$/', $res2) > 0);
    }

    public function testSecondRandomCall() {
        $target = new ExampleService();

        $res1 = $target->getOtherValue();
        $res2 = $target->getOtherValue();

        $this->assertTrue($res1 != $res2);
        $this->assertTrue(preg_match('/^[0-9]+$/', $res1) > 0);
        $this->assertTrue(preg_match('/^[0-9]+$/', $res2) > 0);
    }

}