<?php

namespace PhpProxyBuilder\Proxy;

use PhpProxyBuilder\AroundProxyInterface;
use PhpProxyBuilder\Aop\ProceedingJoinPoint;

/**
 * Example of how to implement a generic proxy class.
 * 
 * You implement a caching logic in one place only. You do the regular 
 * stuff but delegate to unknown method on unknown object. 
 * 
 * Indirection via ProceedingJoinPoint to guard interfaces and ensure dynamic assembly.
 */
class ArrayCachingProxy implements AroundProxyInterface {

    private $someCacheStore;
    private $maxItems;

    /**
     * You configure your caching proxy instance when you create it, from then on 
     * it keeps doing what it was designed for.
     * 
     * In real implementation you would inject some Cache backend instance in constructor but
     * in this implementation we just build a hash map in an array
     * 
     * @param int $maxItems max number of items in cache at any time
     */
    public function __construct($maxItems = 100) {
        $this->maxItems = $maxItems;
    }

    /**
     * Method called instead of the target object. You have a chance to do whatever you need:
     * For example reject the call, change results etc
     * 
     * @param PhpProxyBuilder\Aop\ProceedingJoinPoint $jointPoint
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPoint $jointPoint) {

        $hash = md5(serialize(array(
                    get_class($jointPoint->getTarget()),
                    $jointPoint->getMethodName(),
                    $jointPoint->getArgs()
                )));
        if (!isset($this->someCacheStore[$hash])) {
            // remove some item from cache if we reached capacity
            if (count($this->someCacheStore) >= $this->maxItems) {
                array_shift($this->someCacheStore);
            }

            $this->someCacheStore[$hash] = $jointPoint->proceed();
        }
        return $this->someCacheStore[$hash];
    }

}