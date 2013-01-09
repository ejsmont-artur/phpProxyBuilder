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

use Exception;
use PhpProxyBuilder\AroundProxy;
use PhpProxyBuilder\Aop\ProceedingJoinPoint;
use PhpProxyBuilder\Adapter\CircuitBreaker;
use PhpProxyBuilder\Adapter\CircuitBreaker\ServiceUnavailableException;

/**
 * Class allows adding Circuit Breaker behaviour to any service object by proxying calls.
 * 
 * @link http://artur.ejsmont.org/blog/circuit-breaker
 * 
 * Proxy instance is configured for particular service type. You create CircuitBreakerProxy instances
 * separately for each service you want to proxy. Service name is held as private member as clients' code
 * does not know that CB is in use so they are not able to provide the service name at call time.
 * 
 * @package PublicApi
 */
class CircuitBreakerProxy implements AroundProxy {

    /**
     * @var CircuitBreaker instance of the breaker to be used
     */
    private $breaker;

    /**
     * @var string CircuitBreakerProxy instance is configured to work for particular service only
     */
    private $serviceName;

    /**
     * Proxy instance is configured for particular service type. You create CircuitBreakerProxy instances
     * separately for each service you want to proxy. Service name is held as private member as clients' code
     * does not know that CB is in use so they are not able to provide the service name at call time.
     * 
     * @param CircuitBreaker $breaker
     * @param type $serviceName 
     */
    public function __construct(CircuitBreaker $breaker, $serviceName) {
        $this->breaker = $breaker;
        $this->serviceName = $serviceName;
    }

    /**
     * In this implementation we ask CircuitBreaker implementation if it is safe to proceed with the service call.
     * 
     * @param ProceedingJoinPoint $jointPoint
     * @throws ServiceUnavailableException
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPoint $jointPoint) {

        if ($this->breaker->isAvailable($this->serviceName)) {
            try {
                $result = $jointPoint->proceed();
                $this->breaker->reportSuccess($this->serviceName);
                return $result;
            } catch (Exception $e) {
                $this->breaker->reportFailure($this->serviceName);
                throw $e;
            }
        } else {
            throw new ServiceUnavailableException("CircuitBreakerProxy has denied access to " . $this->serviceName);
        }
    }

}