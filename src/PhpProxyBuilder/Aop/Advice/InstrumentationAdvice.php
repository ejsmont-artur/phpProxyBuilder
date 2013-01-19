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
use PhpProxyBuilder\Adapter\InstrumentorInterface;
use Exception;

/**
 * Class allows addin metrics (timers and counters) to arbitrary objects.
 * 
 * This approach is best suitable for remote call services and code that is relatively slow. Tracking calls
 * and times for every method on objects in tight loops may add significant execution time.
 * 
 * Good use case examples:
 *  - wrap mailer object
 *  - wrap web service client
 *  - wrap database querying service
 *  - wrap batch processors etc
 *  - wrap classes performing significant amount of calculations (1ms per method call or more)
 * 
 * Bad use case examples:
 *  - DTO - data transfer objects
 *  - Objects used in tight loops (called hundreds of times per request)
 * 
 * @package PublicApi
 */
class InstrumentationAdvice implements AroundAdviceInterface {

    const SUFFIX_EXCEPTION = '.error';
    const SUFFIX_SUCCESS = '.success';

    /**
     * @var InstrumentorInterface instance of the metrics gathering instance
     */
    private $monitor;

    /**
     * @var string Optional prefix for the measurements
     */
    private $namePrefix;

    /**
     * @var boolean $metricPerMethod when true metrics are collected for each method separately
     */
    private $metricPerMethod;

    /**
     * Proxy is configured once for all services or per service. If you use proxied objects you may be better off
     * using proxy per target type as in
     * 
     * Examples:
     *  - If all methods on my service are remote calls and i want to track them all together i could name the proxy and
     *      aggregate stats for all methods together like this:
     *      new InstrumentationProxy($monitor, 'ProfileService');
     *  - If my service has slow and fast methods i may want to track them separately
     *      new InstrumentationProxy($monitor, 'ProfileService', true);
     * 
     * @param InstrumentorInterface $monitor
     * @param string $namePrefix optional name to be used for timers prefix, if null target class name is used
     * @param boolean $metricPerMethod when true metrics are collected for each method separately
     */
    public function __construct(InstrumentorInterface $monitor, $namePrefix = null, $metricPerMethod = false) {
        $this->monitor = $monitor;
        $this->namePrefix = $namePrefix;
        $this->metricPerMethod = $metricPerMethod;
    }

    /**
     * In this implementation we measure time and count every method call
     * 
     * @param ProceedingJoinPointInterface $jointPoint
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPointInterface $jointPoint) {
        $time = $this->monitor->getTime();

        if ($this->namePrefix) {
            $name = $this->namePrefix;
        } else {
            $name = get_class($jointPoint->getTarget());
        }
        if ($this->metricPerMethod) {
            $name .= '.' . $jointPoint->getMethodName();
        }

        try {
            $result = $jointPoint->proceed();
            $this->monitor->incrementTimer($name . self::SUFFIX_SUCCESS, $time);
            $this->monitor->incrementCounter($name . self::SUFFIX_SUCCESS);
            return $result;
        } catch (Exception $e) {
            $this->monitor->incrementTimer($name . self::SUFFIX_EXCEPTION, $time);
            $this->monitor->incrementCounter($name . self::SUFFIX_EXCEPTION);
            throw $e;
        }
    }

}