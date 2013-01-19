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
use PhpProxyBuilder\Adapter\LogInterface;

/**
 * Class allows adding logging method calls on a objects by proxying.
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
     * @var LogInterface instance of the logger to be used
     */
    private $logger;

    /**
     * @var int $includeData one of the constants from LoggingProxy
     */
    private $includeData;

    /**
     * Examples:
     *      You can log all method calls with their parameters:
     *          new LoggingProxy("Email", $logger, LoggingAdvice::LOG_RESULT);
     * 
     * Flags indicate what should be logged along with the method name and time.
     *      LoggingAdvice::LOG_BASIC_DATA_ONLY - just method name and time
     *      LoggingAdvice::LOG_ARGUMENTS - include method arguments
     *      LoggingAdvice::LOG_RESULT - include result of the method call
     *      LoggingAdvice::LOG_ARGUMENTS_AND_RETURN - include arguments and result of the method call
     * 
     * @param LogInterface    $logger  Logger to be used
     * @param string $name    You give name to the LoggingProxy to identify different instances
     * @param int    $flags   One of the constants from LoggingProxy
     */
    public function __construct(LogInterface $logger, $name, $flags = self::LOG_BASIC_DATA_ONLY) {
        $this->logger = $logger;
        $this->name = $name;
        $this->flags = (int) $flags;
    }

    /**
     * In this implementation we log each method call with execution time and optional param/result
     * 
     * @param ProceedingJoinPointInterface $jointPoint
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPointInterface $jointPoint) {
        $methodName = $jointPoint->getMethodName();

        // binary & operator
        if (self::LOG_ARGUMENTS & $this->flags) {
            $attachment = $jointPoint->getArguments();
        } else {
            $attachment = null;
        }
        $msg = sprintf("Proxy %s::%s ... ", $this->name, $methodName);
        $this->logger->logDebug($msg, $attachment);

        $startTime = microtime(true);
        $result = $jointPoint->proceed();
        $endTime = microtime(true);

        // binary & operator
        if (self::LOG_RESULT & $this->flags) {
            $attachment = $result;
        } else {
            $attachment = null;
        }
        $msg = sprintf("Proxy %s::%s returned in %.4f; ", $this->name, $methodName, ($endTime - $startTime));
        $this->logger->logDebug($msg, $attachment);

        return $result;
    }

}