<?php

namespace PhpProxyBuilder;

use PhpProxyBuilder\Aop\ProceedingJoinPoint;

/**
 * This is the interface you need to implement for your class to become a generic proxy.
 * 
 * By implementing this method you are able to intercept any method call similar to __call().
 * The difference is that you are a distinct separate object and you can indirectly delegate to the 
 * target object by $jointPoint->proceed();
 * 
 * This interface is called an "Advice" in AOP terms.
 */
interface AroundProxyInterface {

    /**
     * Method called instead of the target object. You have a chance to do whatever you need:
     * For example reject the call, change results etc
     * 
     * @param PhpProxyBuilder\Aop\ProceedingJoinPoint $jointPoint
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPoint $jointPoint);
}