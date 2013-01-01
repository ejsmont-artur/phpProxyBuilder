<?php

namespace Proxy\Tests\Unit\Proxy;

use \Proxy\Proxy;

class ProxyTest extends \PHPUnit_Framework_TestCase
{
	public function testSum()
	{
		$p = new \Proxy\Proxy();
		$this->assertEquals(2, $p->sum(1, 1));
		$this->assertEquals(2, $p->sum(1, 3));
	}
}