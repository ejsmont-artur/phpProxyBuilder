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
use PhpProxyBuilder\Adapter\Log;

/**
 * Class allows adding logging to all/selected method calls on a selected object by proxying.
 * 
 * @package PublicApi
 */
class LoggingAdvice implements AroundAdviceInterface {
    /**
     * Log only the basic data no arguments nor results logged 
     */

    const LOG_BASIC_DATA_ONLY = 0;
    /**
     * Log method arguments at call time for each proxied call.
     */
    const LOG_ARGUMENTS = 1;
    /**
     * Log method return value for each proxied call.
     */
    const LOG_RESULT = 2;
    /**
     * Log both arguments and return values of all proxied calls.
     */
    const LOG_ARGUMENTS_AND_RETURN = 3;

    /**
     * @var string name of the service proxied
     */
    private $name;

    /**
     * @var Log instance of the logger to be used
     */
    private $logger;

    /**
     * @var int $includeData one of the constants from LoggingProxy
     */
    private $includeData;

    /**
     * Examples:
     *  - You can log all method calls by invoking:
     *      new LoggingProxy("Email", $logger);
     * 
     *  - You can log selected methods by providing their names
     *      new LoggingProxy("Email", $logger, array("deliver"));
     * 
     *  - You can include method parameters and return values by changing $includedData
     *      new LoggingProxy("PayPal", $logger", array(), LoggingProxy::LOG_RESULT);
     * 
     * @param string $name you give name to the LoggingProxy to identify different instances
     * @param Log $logger logger to be used

     * @param int $includeData one of the constants from LoggingProxy
     */
    public function __construct($name, Log $logger, $includeData = self::LOG_BASIC_DATA_ONLY) {
        $this->name = $name;
        $this->logger = $logger;
        $this->includeData = $includeData;
    }

    /**
     * In this implementation we log each method call with execution time and optional param/result
     * 
     * @param ProceedingJoinPointInterface $jointPoint
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPointInterface $jointPoint) {
        $methodName = $jointPoint->getMethodName();
        
        // log before            
        if (1 & $this->includeData) {
            $attachment = $jointPoint->getArgs();
        } else {
            $attachment = null;
        }
        $msg = sprintf("Proxy %s::%s calling ...", $this->name, $methodName);
        $this->logger->logDebug($msg, $attachment);

        $startTime = microtime(true);
        $result = $jointPoint->proceed();
        $endTime = microtime(true);

        // log after
        if (2 & $this->includeData) {
            $attachment = $result;
        } else {
            $attachment = null;
        }
        $msg = sprintf("Proxy %s::%s returned in %.4f.", $this->name, $methodName, ($endTime - $startTime));
        $this->logger->logDebug($msg, $attachment);

        return $result;
    }

}