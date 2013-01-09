<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Adapter\Cache;

use PhpProxyBuilder\Adapter\Cache;

/**
 * Simple example of the CacheInterface implementation using in-memory array.
 * This implementation is for demo and testing purpose only but could be useful in real world scenarios
 * if you wanted to create identity map pattern (cache items in memory if values are computed multiple 
 * times during the execution of the script).
 * 
 * @package PrivateComponents
 */
class SimpleArrayCache implements Cache {

    /**
     * @var mixed[] cached values are indexed by key
     */
    private $items = array();

    /**
     * @var int|null null meaning no limit, otherwise max items count
     */
    private $maxSize;

    /**
     * Configure instance
     * 
     * @param int|null $maxItems null meaning no limit, otherwise max items count
     */
    public function __construct($maxItems = null) {
        $this->maxSize = $maxItems;
    }

    /**
     * Load cache item or return null if not present.
     * Obviously there is no way to know if value was in cache if it was set to null.
     * 
     * @param string $key
     * @return mixed|null value or null if value was not found
     */
    public function get($key) {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
        return null;
    }

    /**
     * Save value in cache for up to optionl $ttl seconds
     * 
     * @param string    $key    cache key
     * @param mixed     $value  value to cache (will be serialised)
     * @param int       $ttl    optional seconds to live or default value set by implementation
     * @return void
     */
    public function set($key, $value, $ttl = null) {
        // remove oldest item only if reached limit
        if ($this->maxSize) {
            if (count($this->items) >= $this->maxSize) {
                if (!isset($this->items[$key])) {
                    array_shift($this->items);
                }
            }
        }

        $this->items[$key] = $value;
    }

}