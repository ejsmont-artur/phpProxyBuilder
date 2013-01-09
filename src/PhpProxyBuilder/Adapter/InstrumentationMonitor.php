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
 * Minimal interface for code instrumentation (counters and timers).
 * 
 * @package PublicApi
 */
interface InstrumentationMonitor {

    /**
     * Increments named counter by $value
     * 
     * @param string   $name   name of the counter 
     * @param int      $value  value to be added to the counter, default 1
     */
    public function incrementCounter($name, $value = 1);

    /**
     * Get time to be passed later on to the incrementTimer
     * 
     * @return mixed value not to be inspected by client code.
     */
    public function getTime();

    /**
     * Increments named timer.
     * 
     * @param   string  $name   name of the timer
     * @param   mixed   $time   time value acquired from getTime() method
     * @return void
     */
    public function incrementTimer($name, $time);
}