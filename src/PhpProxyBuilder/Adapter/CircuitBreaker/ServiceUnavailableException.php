<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Adapter\CircuitBreaker;

use PhpProxyBuilder\PhpProxyException;

/**
 * Exception thrown by the PhpProxyBuilder\Adapter\CircuitBreaker implementations
 * in case of service access being denied by the circuit breaker instance.
 * 
 * @package PublicApi
 */
class ServiceUnavailableException extends PhpProxyException {
    
}