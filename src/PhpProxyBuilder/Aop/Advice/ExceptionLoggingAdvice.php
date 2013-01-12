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
use Exception;

/**
 * Class logging all exceptions thrown from proxied methods.
 * 
 * @package PublicApi
 */
class ExceptionLoggingAdvice implements AroundAdviceInterface {

    /**
     * @var Log instance of the logger to be used
     */
    private $logger;

    /**
     * @var int max depth of stack trace to be logged
     */
    private $maxTraceDepth;

    /**
     * Configure proxy
     * 
     * @param Log $logger           logger to be used
     * @param int $maxTraceDepth    log stack trace just to a limited depth to avoid heavy performance impact
     */
    public function __construct(Log $logger, $maxTraceDepth = 30) {
        $this->logger = $logger;
        $this->maxTraceDepth = $maxTraceDepth;
    }

    /**
     * In this implementation we log each exception and rethrow.
     * 
     * @param ProceedingJoinPointInterface $jointPoint
     * @throws \Exception
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPointInterface $jointPoint) {
        try {
            return $jointPoint->proceed();
        } catch (Exception $e) {
            $methodName = $jointPoint->getMethodName();
            $partialTrace = array();
            $trace = $e->getTrace();
            $depth = 0;
            foreach ($trace as $entry) {
                if ($depth > $this->maxTraceDepth) {
                    $partialTrace[] = "... trace truncated";
                    break;
                }
                $file = isset($entry['file']) ? $entry['file'] : "";
                $function = isset($entry['function']) ? $entry['function'] : "";
                $line = isset($entry['line']) ? $entry['line'] : "";

                $partialTrace[] = sprintf("%s:%s:%s", $file, $function, $line);
                $depth++;
            }

            $msg = sprintf("Exception in %s:%s:%s (%d) %s", $e->getFile(), $e->getLine(), $methodName, $e->getCode(), $e->getMessage());
            $this->logger->logWarning($msg, implode("\n", $partialTrace));
            throw $e;
        }
    }

}