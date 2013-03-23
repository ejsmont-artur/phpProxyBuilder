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

use \Closure;
use PhpProxyBuilder\Aop\AroundAdviceInterface;
use PhpProxyBuilder\Aop\ProceedingJoinPointInterface;

/**
 * Class allows to add a closure as a advice, the closure will get instance of ProceedingJoinPointInterface and
 * can use it do proceed as other advices would.
 * 
 * Closure passed in the constructor will be invoked with instances of
 * PhpProxyBuilder\Aop\ProceedingJoinPointInterface
 * 
 * @see PhpProxyBuilder\Aop\ProceedingJoinPointInterface
 * 
 * @package PublicApi
 */
class ClosureAdvice implements AroundAdviceInterface {

    /**
     * @var Closure method to be invoked with ProceedingJoinPointInterface
     */
    private $closure;

    /**
     * Configure closure based advice.
     * 
     * @param Closure $closure closure to be called for all proxied methods
     */
    public function __construct(Closure $closure) {
        $this->closure = $closure;
    }

    /**
     * In this implementation we call the provided closure.
     * 
     * @param ProceedingJoinPointInterface $jointPoint
     * @throws ServiceUnavailableException|Exception
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPointInterface $jointPoint) {
        return $this->closure->__invoke($jointPoint);
    }

}