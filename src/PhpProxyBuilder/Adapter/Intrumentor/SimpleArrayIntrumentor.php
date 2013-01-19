<?php

/**
 * This file is part of the PhpProxyBuilder package.
 *
 * @link https://github.com/ejsmont-artur/phpProxyBuilder
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProxyBuilder\Adapter\Intrumentor;

use PhpProxyBuilder\Adapter\InstrumentorInterface;

/**
 * Simple example of the Intrumentor implementation using in-memory array.
 * 
 * This implementation is for demo and testing purpose only but could be useful in real world scenarios
 * if you wanted to create identity map pattern (cache items in memory if values are computed multiple 
 * times during the execution of the script).
 * 
 * @package PrivateComponents
 */
class SimpleArrayIntrumentor implements InstrumentorInterface {

    /**
     * @var mixed[] metrics by name
     */
    private $counters = array();

    /**
     * @var mixed[] metrics by name
     */
    private $timers = array();

    /**
     * Get time to be passed later on to the incrementTimer
     * 
     * @return mixed value not to be inspected by client code.
     */
    public function getTime() {
        return microtime(true);
    }

    /**
     * Increments named counter by $value
     * 
     * @param string   $name   name of the counter 
     * @param int      $value  value to be added to the counter, default 1
     * @return InstrumentorInterface returns $this for chaining
     */
    public function incrementCounter($name, $value = 1) {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = 0;
        }
        $this->counters[$name] += $value;
    }

    /**
     * Increments named timer.
     * 
     * @param   string  $name   name of the timer
     * @param   mixed   $time   time value acquired from getTime() method
     * @return InstrumentorInterface returns $this for chaining
     */
    public function incrementTimer($name, $time) {
        if (!isset($this->timers[$name])) {
            $this->timers[$name] = 0;
        }
        $this->timers[$name] += $this->getTime() - $time;
    }

    /**
     * Extra admin method allowing to export metrics
     * 
     * @param string $name name of the timer
     * @return double
     */
    public function getTimer($name) {
        if (!isset($this->timers[$name])) {
            return 0;
        } else {
            return $this->timers[$name];
        }
    }

    /**
     * Extra admin method allowing to export metrics
     * 
     * @param string $name name of the counter
     * @return double
     */
    public function getCounter($name) {
        if (!isset($this->counters[$name])) {
            return 0;
        } else {
            return $this->counters[$name];
        }
    }

}