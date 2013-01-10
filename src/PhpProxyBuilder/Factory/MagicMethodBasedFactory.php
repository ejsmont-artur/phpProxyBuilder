<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Factory;

use PhpProxyBuilder\ProxyFactoryInterface;
use PhpProxyBuilder\Aop\AroundAdviceInterface;
use PhpProxyBuilder\Proxy\MagicMethodProxy;

/**
 * This class is rsponsible for creation of proxy instances.
 * 
 * This implementation creates proxies based on simeple method delegation and magic methods. 
 * These proxies will not pass type checks as they do not implement any interfaces of the target object.
 * If you never use instanceof nor typehints this could be enough though as a least intrusive option.
 * 
 * @package PublicApi
 */
class MagicMethodBasedFactory implements ProxyFactoryInterface {

    /**
     * Wraps object and delegates selected method calls to advice.
     * 
     * Other factory implementations will have more exciting logic :)
     *  
     * @param AroundAdviceInterface $proxy implementation of the advice
     * @param mixed     $target instance to be wrapped by the proxy
     * @param string[]  $methodNames list of methods that should be delegated to advice, others go directly to target
     * @return mixed returns proxied $target instance
     */
    public function createProxy(AroundAdviceInterface $proxy, $target, $methodNames = array()) {
        return new MagicMethodProxy($proxy, $target, $methodNames);
    }

}