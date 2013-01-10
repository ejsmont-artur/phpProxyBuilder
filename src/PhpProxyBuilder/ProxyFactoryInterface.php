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

use PhpProxyBuilder\Aop\AroundAdviceInterface;

/**
 * Interface used to create proxied instances of a target object.
 * 
 * Any instance can be wrapped around with any AroundAdviceInterface implementation.
 * 
 * @package PublicApi
 */
interface ProxyFactoryInterface {

    /**
     * Wraps object and delegates selected method calls to advice.
     *  
     * @param AroundAdviceInterface $proxy implementation of the advice
     * @param mixed     $target instance to be wrapped by the proxy
     * @param string[]  $methodNames list of methods that should be delegated to advice, others go directly to target
     * @return mixed returns proxied $target instance
     */
    public function createProxy(AroundAdviceInterface $proxy, $target, $methodNames = array());
}