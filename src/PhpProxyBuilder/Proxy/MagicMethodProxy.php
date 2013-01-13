<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Proxy;

use PhpProxyBuilder\Aop\AroundAdviceInterface;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;
use PhpProxyBuilder\Aop\JoinPoint\DynamicJoinPoint;

/**
 * Class provides simple proxy functionality without typehint, static methods etc.
 * 
 * Simplistic but fast and does not require any dependencies.
 * 
 * WARNING - this method of proxying does not support type hints and does not pass instanceof checks.
 * 
 * @package PrivateComponents
 */
class MagicMethodProxy {

    /**
     * @var DynamicJoinPoint used to delegate method calls to target
     */
    private $joinPoint;

    /**
     * @var AroundAdviceInterface proxy with additional logic
     */
    private $proxy;

    /**
     * @var string[] $methods methods names to be logged, if empty all method calls are logged
     */
    private $methods = array();

    /**
     * Configure instance of a proxy
     * 
     * @param AroundAdviceInterface $proxy AroundAdviceInterface implementing object that will intercept method calls
     * @param mixed $target proxied object
     * @param string[] $methodNames list of methods to be intercepted, if empty all methods are proxied
     */
    public function __construct(AroundAdviceInterface $proxy, $target, $methodNames = array()) {
        $this->proxy = $proxy;
        $this->joinPoint = new DynamicJoinPoint($target);

        // inverse for faster lookups
        foreach ($methodNames as $method) {
            $this->methods[$method] = true;
        }
    }

    /**
     * Intercpets all method calls and passes through to the target instance.
     *
     * @param string    $methodName method to be intercepted
     * @param mixed[]   $arguments  arguments
     * @return mixed returned by proxy or target
     */
    public function __call($methodName, $arguments) {
        $this->joinPoint->setMethodCall($methodName, $arguments);
        if (empty($this->methods) || isset($this->methods[$methodName])) {
            // execute through the AroundAdviceInterface
            return $this->proxy->interceptMethodCall($this->joinPoint);
        } else {
            // execute directly through the joinPoint
            return $this->joinPoint->proceed();
        }
    }

    /**
     * Intercepts access to all properties and delegates to the target.
     * 
     * @param string $name
     * @param mixed $value 
     * @return void
     */
    public function __set($name, $value) {
        $this->joinPoint->getTarget()->$name = $value;
    }

    /**
     * Intercepts access to all properties and delegates to the target.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->joinPoint->getTarget()->$name;
    }

    /**
     * Intercepts calls to __toString and delegates to target by casting it to string
     * 
     * @return string
     */
    public function __toString() {
        return ((string) $this->joinPoint->getTarget());
    }

    /**
     * Intercepts calls to __sleep and delegates to target
     * 
     * @return string[]
     */
    public function __sleep() {
        return $this->joinPoint->getTarget()->__sleep();
    }

    /**
     * Intercepts calls to __wakeup and delegates to target
     * 
     * @return void
     */
    public function __wakeup() {
        $this->joinPoint->getTarget()->__wakeup();
    }

    /**
     * Clears the instance.
     * 
     * Excplicitly call destructor on the 
     * 
     * @return void
     */
    public function __destruct() {
        if (isset($this->joinPoint)) {
            if (is_object($this->joinPoint) && method_exists($this->joinPoint, "__destruct")) {
                $this->joinPoint->__destruct();
            }

            unset($this->joinPoint);
            unset($this->proxy);
            unset($this->methods);
        }
    }

}