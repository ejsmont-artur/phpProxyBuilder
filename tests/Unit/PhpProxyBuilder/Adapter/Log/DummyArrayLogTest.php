<?php

namespace Tests\Unit\PhpProxyBuilder\Adapter\Log;

use PhpProxyBuilder\Adapter\LogInterface;
use PhpProxyBuilder\Adapter\Log\DummyArrayLog;

class DummyArrayLogTest extends \PHPUnit_Framework_TestCase {

    public function testDebug() {
        $logger = new DummyArrayLog();
        $logger->logDebug("55555");
        $this->assertEquals("debug: 55555", $logger->getLastMessage());
    }

    public function testWarning() {
        $logger = new DummyArrayLog();
        $logger->logWarning("444");
        $this->assertEquals("warning: 444", $logger->getLastMessage());
    }

    public function testError() {
        $logger = new DummyArrayLog();
        $logger->logError("12345");
        $this->assertEquals("error: 12345", $logger->getLastMessage());
    }

}