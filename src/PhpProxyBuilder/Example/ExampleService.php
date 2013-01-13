<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Example;

/**
 * Service for tests and examples, not to be used as has no value.
 * 
 * Note that service is a Plain Old PHP Object. It is not aware of any proxying, it also has examples of some
 * of potential PHP features.
 * 
 * All the magic methods etc are just to test and demonstrate what can be proxied.
 * 
 * @package PrivateComponents
 */
class ExampleService {

    /**
     * Example of unstable method for some more testing
     * 
     * @return string
     */
    public function getRandomValue($prefix, $suffix) {
        return $prefix . mt_rand(1, 1000000) . $suffix;
    }

    /**
     * Example of unstable method for some more testing
     * 
     * @return string
     */
    public function getOtherValue() {
        return time() . mt_rand(1, 1000000);
    }

    // ===============================================================================================================
    // All methods below are just to show what works, your service does not need any of that !
    // ===============================================================================================================

    static $destructorCallCount = 0;
    static $wakeupCallCount = 0;
    private $someState = 0;

    /**
     * Magic method - added just for tests, service does not need that!
     * 
     * @param string $name
     * @param mixed $value 
     */
    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * Magic method - added just for tests, service does not need that!
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->$name;
    }

    /**
     * Example of protected method - added just for tests, service does not need that!
     * 
     * @return int 
     */
    protected function notAllowed() {
        return mt_rand(1, 1000000);
    }

    /**
     * Magic method - added just for tests, service does not need that!
     * 
     * @return string
     */
    public function __toString() {
        return ((string) microtime(true));
    }

    /**
     * Magic method - added just for tests, service does not need that!
     * 
     * @return string[]
     */
    public function __sleep() {
        return array("slept fine");
    }

    /**
     * Magic method - added just for tests, service does not need that!
     * 
     * @return void
     */
    public function __wakeup() {
        self::$wakeupCallCount++;
    }

    /**
     * Method allowing you to inspect and reset the count of __wakeup calls
     * 
     * @param mixed $value number or null
     * @return void
     */
    public function setWakeupCount($value) {
        self::$wakeupCallCount = $value;
    }

    /**
     * Method allowing you to set and reset the count of __wakeup calls
     * 
     * @return void
     */
    public function getWakeupCount() {
        return self::$wakeupCallCount;
    }

    /**
     * Magic method - added just for tests, service does not need that!
     * 
     * @return string
     */
    public function __destruct() {
        self::$destructorCallCount++;
    }

    /**
     * Method allowing you to set and reset the count of __destruct calls
     * 
     * @param mixed $value number or null
     * @return void
     */
    public function setDestructCount($value) {
        self::$destructorCallCount = $value;
    }

    /**
     * Method allowing you to inspect and reset the count of __destruct calls
     * 
     * @return void
     */
    public function getDestructCount() {
        return self::$destructorCallCount;
    }

}