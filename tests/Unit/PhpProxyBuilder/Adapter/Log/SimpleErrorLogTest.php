<?php

namespace Tests\Unit\PhpProxyBuilder\Adapter\Log;

use PhpProxyBuilder\Adapter\LogInterface;
use PhpProxyBuilder\Adapter\Log\SimpleErrorLog;

class SimpleErrorLogTest extends \PHPUnit_Framework_TestCase {

    private $testLogFilePrefix = '/tmp/PhpProxyBuilder-SimpleErrorLog.log';
    private $testLogFile;

    protected function getFileName() {
        return $this->testLogFilePrefix . microtime(true) . '.log';
    }

    public function setup() {
        parent::setup();
        $this->testLogFile = $this->getFileName();
    }

    public function teardown() {
        @unlink($this->testLogFile);
        parent::teardown();
    }

    // ================================================== TESTS =======================================================

    public function testSanity() {
        $instance = new SimpleErrorLog();
        $this->assertTrue($instance instanceof LogInterface);
    }

    public function testCalls() {
        // overwrite
        $time = time();
        file_put_contents($this->testLogFile, $time);

        $instance = new SimpleErrorLog($this->testLogFile);
        $instance->logDebug('Test debug message');
        $instance->logDebug('Test debug message', 1);
        $instance->logWarning('Test warning message');
        $instance->logWarning('Test warning message', 2);
        $instance->logError('Test error message');
        $instance->logError('Test error message', 3);
        $data = file_get_contents($this->testLogFile);

        $expect = $time . 'Test debug messageTest debug message1Test warning messageTest warning message2Test error messageTest error message3';
        $this->assertEquals($expect, $data);
    }

}