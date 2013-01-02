<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Aop;

/**
 * Interface used in AroundProxy to delegate calls to the target object. 
 * Allows to get method name, target object and parameters. 
 * 
 * In AOP naming JoinPoint is an instance of execution of a method.
 * 
 * We may add more metadata to it later.
 * 
 * @link http://www.eclipse.org/aspectj/doc/released/runtime-api/org/aspectj/lang/ProceedingJoinPoint.html
 * @link http://static.springsource.org/spring/docs/3.0.x/reference/aop.html
 */
interface ProceedingJoinPoint {

    /**
     * Proceed with method execution. In case of nested proxies delegates deeper.
     * 
     * @throws \Exception target can throw exceptions
     * @return mixed result return of the target method 
     */
    public function proceed();

    /**
     * Returns target object being proxied
     * @return mixed target object
     */
    public function getTarget();

    /**
     * Returns name of the target method being called.
     * @return string target method name being called
     */
    public function getMethodName();

    /**
     * Returns array of arguments passed to the intercepted method call.
     * @return mixed[] array of arguments passed to the proxied method 
     */
    public function getArgs();
}
