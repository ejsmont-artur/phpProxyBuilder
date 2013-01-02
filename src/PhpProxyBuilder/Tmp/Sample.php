<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Tmp;

/**
 * Interface used to create proxied instances of a target object.
 * 
 * Any instance can be wrapped around with any AroundProxyInterface implementation.
 * 
 * FIXME - to be removed later
 */
class Sample {

    /**
     * Adds numbers
     *
     * @param int $a
     * @param int $b
     * @return int 
     */
    public function sum($a, $b) {
        return $a + $b;
    }

}