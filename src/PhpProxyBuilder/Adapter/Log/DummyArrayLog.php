<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Adapter\Log;

use PhpProxyBuilder\Adapter\Log;

/**
 * Simple instance for tests, logs by keeping entries in an array.
 * 
 * Good for tests.
 * 
 * @package PrivateComponents
 */
class DummyArrayLog implements Log {

    /**
     * @var string[] log messages
     */
    private $messages = array();

    /**
     * Log message as debug level
     * 
     * @param string $message
     * @param mixed $attachment optional array or structure of data to be attached
     * @return void
     */
    public function logDebug($message, $attachment = null) {
        $this->messages[] = "debug: " . $message . print_r($attachment, true);
    }

    /**
     * Log message as warning level
     * 
     * @param string $message
     * @param mixed $attachment optional array or structure of data to be attached
     * @return void
     */
    public function logWarning($message, $attachment = null) {
        $this->messages[] = "warning: " . $message . print_r($attachment, true);
    }

    /**
     * Log message as error level
     * 
     * @param string $message
     * @param mixed $attachment optional array or structure of data to be attached
     * @return void
     */
    public function logError($message, $attachment = null) {
        $this->messages[] = "error: " . $message . print_r($attachment, true);
    }

    /**
     * Get messages count so far
     * @return type 
     */
    public function getMessagesCount() {
        return count($this->messages);
    }

    /**
     * Get most recent log entry as string
     * @return string
     */
    public function getLastMessage() {
        $key = array_pop(array_keys($this->messages));
        return $this->messages[$key];
    }

}