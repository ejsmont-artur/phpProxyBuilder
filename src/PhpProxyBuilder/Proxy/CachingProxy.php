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
use PhpProxyBuilder\Aop\ProceedingJoinPoint;
use PhpProxyBuilder\Adapter\Cache;

/**
 * Caching proxy is used to intercept and cache all method calls based on:
 *      - target class name
 *      - method name
 *      - serialised arguments
 * 
 * You can either set custom ttl for the CachingProxy or use default ttl from your Cache instance.
 * 
 * @package PublicApi
 */
class CachingProxy implements AroundProxy {

    /**
     * @var Cache instance of cache implementation
     */
    private $cache;

    /**
     * @var int|null time to live in seconds, if null default value of $cache implementation is used.
     */
    private $ttl;

    /**
     * You configure your caching proxy instance when you create it, from then on 
     * it keeps caching method calls.
     * 
     * @param Cache     $cache  cache implementation
     * @param int|null  $ttl    time to live in seconds, if null default value of $cache implementation is used.
     */
    public function __construct(Cache $cache, $ttl = null) {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * Method called instead of the target object. You have a chance to do whatever you need:
     * For example reject the call, change results etc
     * 
     * @param ProceedingJoinPoint $jointPoint
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPoint $jointPoint) {
        $key = md5(serialize(array(
                    get_class($jointPoint->getTarget()),
                    $jointPoint->getMethodName(),
                    $jointPoint->getArguments()
                )));
        $value = $this->cache->get($key);

        if ($value === null) {
            $value = $jointPoint->proceed();
            $this->cache->set($key, $value, $this->ttl);
        }
        return $value;
    }

}