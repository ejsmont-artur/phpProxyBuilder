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
 * Minimalistic cache interface used internally to shield the library from external libraries.
 * Adapters can be provided for different frameworks like ZF2, Symfony2 etc.
 * 
 * @package PublicApi
 */
interface Cache {

    /**
     * Load cache item or return null if not present.
     * Obviously there is no way to know if value was in cache if it was set to null.
     * 
     * @param string $key
     * @return mixed|null value or null if value was not found
     */
    public function get($key);

    /**
     * Save value in cache for up to optionl $ttl seconds
     * @param string    $key    cache key
     * @param mixed     $value  value to cache (will be serialised)
     * @param int       $ttl    optional seconds to live or default value set by implementation
     * @return void
     */
    public function set($key, $value, $ttl = null);
}