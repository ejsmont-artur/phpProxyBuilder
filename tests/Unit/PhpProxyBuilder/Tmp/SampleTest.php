<?php

namespace Tests\Unit\PhpProxyBuilder\Tmp;

use PhpProxyBuilder\Tmp\Sample;

class SampleTest extends \PHPUnit_Framework_TestCase {

    public function testSum() {
        $p = new Sample();
        $this->assertEquals(2, $p->sum(1, 1));
    }

}