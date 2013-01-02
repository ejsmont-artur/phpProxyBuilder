<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @param ProceedingJoinPoint $jointPoint
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPoint $jointPoint);
}