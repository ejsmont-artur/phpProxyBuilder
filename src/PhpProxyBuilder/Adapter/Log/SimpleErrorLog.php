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
 * Minimalistic implementation sending logs to error_log.
 * 
 * This implementation would be used only if your framework does not have any logging facilities.
 * 
 * @package PrivateComponents
 */
class SimpleErrorLog implements Log {

    const LOG_TO_DEFAULT = 0;
    const LOG_TO_FILE = 3;

    /**
     * @link http://php.net/manual/en/function.error-log.php
     * @var string used for error_log
     */
    private $messageType;

    /**
     * @link http://php.net/manual/en/function.error-log.php
     * @var string|null used for error_log
     */
    private $destination;

    /**
     * Sends messages to default logging mechanism or to selected file.
     *
     * @param type $fileName 
     */
    public function __construct($fileName = null) {
        if ($fileName) {
            $this->messageType = self::LOG_TO_FILE;
            $this->destination = $fileName;
        } else {
            $this->messageType = self::LOG_TO_DEFAULT;
            $this->destination = null;
        }
    }

    /**
     * Log message as debug level
     * 
     * @param string $message
     * @param mixed $attachment optional array or structure of data to be attached
     * @return void
     */
    public function logDebug($message, $attachment = null) {
        error_log($message . print_r($attachment, true), $this->messageType , $this->destination);
    }

    /**
     * Log message as warning level
     * 
     * @param string $message
     * @param mixed $attachment optional array or structure of data to be attached
     * @return void
     */
    public function logWarning($message, $attachment = null) {
        error_log($message . print_r($attachment, true), $this->messageType , $this->destination);
    }

    /**
     * Log message as error level
     * 
     * @param string $message
     * @param mixed $attachment optional array or structure of data to be attached
     * @return void
     */
    public function logError($message, $attachment = null) {
        error_log($message . print_r($attachment, true), $this->messageType , $this->destination);
    }

}