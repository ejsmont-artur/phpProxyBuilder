<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Aop\Advice;

use PhpProxyBuilder\Aop\AroundAdviceInterface;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;
use PhpProxyBuilder\Adapter\CircuitBreakerInterface;
use PhpProxyBuilder\Adapter\CircuitBreaker\ServiceUnavailableException;
use Exception;

/**
 * Class allows adding Circuit Breaker behaviour to any service object by proxying calls.
 * 
 * @link http://artur.ejsmont.org/blog/circuit-breaker
 * 
 * Proxy instance is configured for particular service type. You create CircuitBreakerAdvice instances
 * separately for each service you want to proxy. Service name is held as private member as clients' code
 * does not know that CB is in use so they are not able to provide the service name at call time.
 * 
 * @package PublicApi
 */
class CircuitBreakerAdvice implements AroundAdviceInterface {

    /**
     * @var CircuitBreakerInterface instance of the breaker to be used
     */
    private $breaker;

    /**
     * @var string name of the service for circuit breaker to distinguis between services
     */
    private $serviceName;

    /**
     * @var string[] list exception types that indicate service unavailability. 
     */
    private $failOnExceptions = array();

    /**
     * @var Exception exception to be thrown on service failure
     */
    private $throwOnFailure;

    /**
     * Proxy instance is configured for particular service type. You create CircuitBreakerAdvice instances
     * separately for each service you want to proxy. Service name is held as private member as clients' code
     * does not know that CB is in use so they are not able to provide the service name at call time.
     * 
     * Some exceptions may indicate user error not service unavailability so $failOnExceptions can be used to 
     * provide the list of exception names that indicate service failure.
     * 
     * Some exceptions have special constructor requirements so you can provide exception instance to be thrown 
     * when service is offline. Your code should know what exceptions indicate connectivity issues so you create one.
     * If $throw is null proxy throws PhpProxyBuilder\Adapter\CircuitBreaker\ServiceUnavailableException
     * 
     * Circuit breaker should only count connectivity/availability errors otherwise user/code errors could mark
     * service as unavailable causing outage. Please be careful what exception types you provide to $failOnExceptions.
     * Good example:
     *      new CircuitBreakerAdvice($breaker, "PaymentService", array(
     *          "ConnectionTimeoutException",
     *          "ConnectionErrorException",
     *          "InternalServerErrorException",
     *          ), new ConnectionTimeoutException());
     * In this example sevice could thow some other error types like insufficient funds etc but they would not
     * be considered service availability failures.
     * 
     * @param CircuitBreakerInterface $breaker     Instance of circuit breaker
     * @param type $serviceName           Service name for the circuit breaker to use
     * @param string[] $failOnExceptions  list of exception names indicating service unavailability
     * @param Exception $throw            this exception will be thrown when service becomes unavailable, if null 
     */
    public function __construct(CircuitBreakerInterface $breaker, $serviceName, $failOnExceptions = array(), $throw = null) {
        $this->breaker = $breaker;
        $this->serviceName = $serviceName;

        foreach ($failOnExceptions as $exceptionName) {
            $this->failOnExceptions[] = $exceptionName;
        }

        if (!empty($throw) && $throw instanceof Exception) {
            $this->throwOnFailure = $throw;
        } else {
            $this->throwOnFailure = new ServiceUnavailableException("CircuitBreakerAdvice blocked " . $this->serviceName);
        }
    }

    /**
     * In this implementation we ask CircuitBreaker implementation if it is safe to proceed with the service call.
     * 
     * @param ProceedingJoinPointInterface $jointPoint
     * @throws ServiceUnavailableException|Exception
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPointInterface $jointPoint) {

        if ($this->breaker->isAvailable($this->serviceName)) {
            try {
                $result = $jointPoint->proceed();
                $this->breaker->reportSuccess($this->serviceName);
                return $result;
            } catch (Exception $e) {
                if ($this->isServiceFailureException($e)) {
                    $this->breaker->reportFailure($this->serviceName);
                }else{
                    $this->breaker->reportSuccess($this->serviceName);
                }
                throw $e;
            }
        } else {
            throw $this->throwOnFailure;
        }
    }

    /**
     * Helper checks if the exception that was caught means a service failure or just a regular user/code error.
     * Circuit breaker should only count connectivity/availability errors otherwise user/code errors could mark
     * service as unavailable causing outage. Please be careful what exception types you provide to $failOnExceptions.
     * 
     * Good example:
     *      new CircuitBreakerAdvice($breaker, "Payments", array(
     *          "ConnectionTimeoutException",
     *          "ConnectionErrorException",
     *          "InternalServerErrorException",
     *          ), new ConnectionTimeoutException());
     * 
     * @param Exception $e exception to check
     * @return boolean 
     */
    protected function isServiceFailureException(Exception $e) {
        if (empty($this->failOnExceptions)) {
            // if $failOnExceptions array was empty then any exception type is considered service failure 
            return true;
        } else {
            foreach ($this->failOnExceptions as $typeName) {
                if ($e instanceof $typeName) {
                    return true;
                }
            }
            return false;
        }
    }

}