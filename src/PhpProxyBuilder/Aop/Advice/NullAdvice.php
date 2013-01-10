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
use PhpProxyBuilder\Adapter\Cache;

/**
 * Delegates without doing anything, good for testing or if replacement needed.
 * 
 * @package PublicApi
 */
class NullAdvice implements AroundAdviceInterface {

    /**
     * delegates withough doing anything.
     * 
     * @param ProceedingJoinPointInterface $jointPoint
     * @return mixed 
     */
    public function interceptMethodCall(ProceedingJoinPointInterface $jointPoint) {
        return $jointPoint->proceed();
    }

}