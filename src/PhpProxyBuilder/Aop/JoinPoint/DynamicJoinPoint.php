<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Aop\JoinPoint;

use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;

/**
 * Class allowing modification of existing joint point at runtime
 * 
 * @link http://www.eclipse.org/aspectj/doc/released/runtime-api/org/aspectj/lang/ProceedingJoinPoint.html
 * @link http://static.springsource.org/spring/docs/3.0.x/reference/aop.html
 * 
 * @package PrivateComponents
 */
class DynamicJoinPoint implements ProceedingJoinPointInterface {

    /**
     * @var mixed target to delegate to
     */
    private $target;

    /**
     * @var string method name
     */
    private $methodName;

    /**
     * @var mixed[] method arbuments
     */
    private $arguments;

    /**
     * Configure instance
     * 
     * @param mixed $target object to delegate method calls to
     */
    public function __construct($target) {
        $this->target = $target;
    }

    /**
     * Proceed with method execution. In case of nested proxies delegates deeper.
     * 
     * @throws \Exception target can throw exceptions
     * @return mixed result return of the target method 
     */
    public function proceed() {
        
    }

    /**
     * Returns target object being proxied
     * @return mixed target object
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Returns name of the target method being called.
     * @return string target method name being called
     */
    public function getMethodName() {
        return $this->methodName;
    }

    /**
     * Returns array of arguments passed to the intercepted method call.
     * @return mixed[] array of arguments passed to the proxied method 
     */
    public function getArguments() {
        return $this->arguments;
    }

    // ============================== extended interface methods for the framework only ===============================

    /**
     * Allows to change existing joinPoint properties
     * 
     * @param string    $name       method name
     * @param mixed     $arguments  arguments
     * @return void
     */
    public function setMethodCall($name, $arguments) {
        $this->methodName = $name;
        $this->arguments = $arguments;
    }

}
