<?php

namespace PhpProxyBuilder\Aop;

/**
 * Interface passed to around proxy.
 * 
 * Allows to ger method name, target object and parameters. 
 * 
 * We may add more metadata to it later.
 * 
 * @see http://www.eclipse.org/aspectj/doc/released/runtime-api/org/aspectj/lang/ProceedingJoinPoint.html
 * 
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
     * @return mixed target object that is being proxied 
     */
    public function getTarget();

    /**
     * @return string target method being called
     */
    public function getMethodName();

    /**
     * @return mixed[] array of arguments passed to the proxied method 
     */
    public function getArgs();
}
