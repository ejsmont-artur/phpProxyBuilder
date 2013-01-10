<?php

namespace Tests\Unit\PhpProxyBuilder\Example;

use PhpProxyBuilder\Example\ExampleService;

class ExampleServiceTest extends \PHPUnit_Framework_TestCase {

    public function testSum() {
        $p = new ExampleService();
        $this->assertEquals(2, $p->sum(1, 1));
    }

}