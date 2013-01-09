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

use PhpProxyBuilder\AroundProxy;
use PhpProxyBuilder\Aop\Implementation\DynamicJoinPoint;

/**
 * Class provides simple proxy functionality without typehint, static methods etc.
 * 
 * Simplistic but fast and does not require any dependencies.
 * 
 * WARNING - this method of proxying does not support type hints and does not pass instanceof checks.
 * 
 * @package PrivateComponents
 */
class MagicMethodProxyWrapper {

    /**
     * @var DynamicJoinPoint used to delegate method calls to target
     */
    private $joinPoint;

    /**
     * @var AroundProxy proxy with additional logic
     */
    private $proxy;

    /**
     * @var string[] $methods methods names to be logged, if empty all method calls are logged
     */
    private $methods = array();

    /**
     * Configure instance of a proxy
     * 
     * @param AroundProxy   $proxy          AroundProxy implementing object that will intercept method calls
     * @param mixed         $target         proxied object
     * @param string[]      $methodNames    list of methods to be intercepted, if empty all methods are proxied
     */
    public function __construct(AroundProxy $proxy, $target, $methodNames = array()) {
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
     * WARNING - this method of proxying does not support type hints and does not pass instanceof checks.
     *
     * @param string    $methodName method to be intercepted
     * @param mixed[]   $arguments  arguments
     * @return mixed returned by proxy or target
     */
    public function __call($methodName, $arguments) {
        if (empty($this->methods) || isset($this->methods[$methodName])) {
            // execute through the AroundProxy
            $this->joinPoint->setMethodCall($methodName, $arguments);
            return $this->proxy->interceptMethodCall($this->joinPoint);
        } else {
            // execute directly through the joinPoint
            return $this->joinPoint->proceed();
        }
    }

}