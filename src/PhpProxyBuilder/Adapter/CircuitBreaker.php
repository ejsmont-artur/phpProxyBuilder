<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Adapter;

/**
 * Minimal interface for Circuit Breaker adapters to implement.
 * 
 * After failing/succeeding to connect failure/success has to be reported to Circuit Breaker
 * This way CB can keep track of which service is available and allow controlled retries to broken services.
 *
 * Service names can by any strings. If you want to set custom thresholds you have to make them match
 * the passed config.
 * 
 * Circuit breaker counts each failure and once you reach limit it will stop allowing you to connect.
 * You can also set retry timeout per service. Then after retry timeout seconds CB will allow one
 * thread to try to connect to the service again. If thread fails wait till retry timeout. If thread 
 * succeeds more threads will be allowed to connect.
 * 
 * Typical simplified user code would look like this:
 * 
 * $result = false;
 * if( $cb->isAvailable('myServiceName') ){
 *   if( HoweverYouConnectToTheService() ){
 *     $result = true;
 *     $cb->reportSuccess('myServiceName');
 *   }else{
 *     $cb->reportFailure('myServiceName');
 *   }
 * }
 * 
 * @package PublicApi 
 */
interface CircuitBreaker {

    /**
     * Check if service is available (according to CB knowledge)
     * 
     * @param string $serviceName - arbitrary service name 
     * @return boolean true if service is available, false if service is down
     */
    public function isAvailable($serviceName);

    /**
     * Use this method to let CB know that you failed to connect to the 
     * service of particular name.
     * 
     * Allows CB to update its stats accordingly for future HTTP requests.
     * 
     * @param string $serviceName - arbitrary service name 
     * @return void
     */
    public function reportFailure($serviceName);

    /**
     * Use this method to let CB know that you successfully connected to the 
     * service of particular name.
     * 
     * Allows CB to update its stats accordingly for future HTTP requests.
     * 
     * @param string $serviceName - arbitrary service name 
     * @return void
     */
    public function reportSuccess($serviceName);
}