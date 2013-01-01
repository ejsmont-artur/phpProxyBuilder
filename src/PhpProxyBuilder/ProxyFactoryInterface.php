<?php

namespace PhpProxyBuilder;

use PhpProxyBuilder\AroundProxyInterface;

/**
 * Interface used to create proxied instances of a target object.
 * 
 * Any instance can be wrapped around with any AroundProxyInterface implementation.
 * 
 */
interface ProxyFactoryInterface {

    /**
     * Wraps object and delegates all method calls to proxy.
     *  
     * @param PhpProxyBuilder\AroundProxyInterface $proxy implementation of the proxy
     * @param mixed $target instance to be wrapped by the proxy
     * @return mixed returns instance passing all type checks of original $target instance
     */
    public function createSimpleProxy(AroundProxyInterface $proxy, $target);
}