<?php

// THIS IS JUST A 30min HACK TO SHOW THE CONCEPT - THIS CODE WAS NOT EVEN RAN.

/**
 * Just an example of any arbitrary interface. I want to be able to add 
 * functionality to this interface without extending it, i want to use 
 * proxy/decorator pattern and delegate instead.
 */
interface Calculator {

    public function add($a, $b);

    public function substract($a, $b);
}

/**
 * Some implementation, business logic is irrelevant. Service class is slow.
 */
class SlowCalculator {

    public function add($a, $b) {
        sleep(10);
        return $a + $b;
    }

    public function substract($a, $b) {
        sleep(10);
        return $a - $b;
    }

}

/**
 * Library exposes a few interfaces, AroundProxy is one of them. 
 * By implementing this method you are able to intercept method call to 
 * object instance and do something about it. Good use cases are same as for AOP:
 *      - caching
 *      - security
 *      - logging
 *      - error handling/reporting
 *      - metrics, measurements, profiling
 *      - user tracking / audit trail
 *      - 
 */
interface AroundProxy {

    public function interceptMethodCall($targetObject, $methodName, $arguments);
}

/**
 * You implement a caching logic in one place only. You do the regular 
 * stuff but delegate to unknown method on unknown object.
 * 
 * PS. This would be made nicer so that you would not have  call_user_func_array. 
 * We would do it similar to AOP so you would call framework's delegator. 
 * I use call_user_func_array for simplicity here.
 */
class CachingAroundProxy implements AroundProxy {

    private $someCacheStore;
    private $maxItems;

    /**
     * You configure your caching proxy instance when you create it, from then on 
     * it keeps doing what it was designed for.
     * @param int $maxItems max number of items in cache at any time
     */
    public function __construct($maxItems = 100) {
        $this->maxItems = $maxItems;
    }

    public function interceptMethodCall($targetObject, $methodName, $arguments) {
        $hash = md5(serialize(array(get_class($targetObject), $methodName, $arguments)));
        if (!isset($this->someCacheStore[$hash])) {
            // remove some item from cache if we reached capacity
            if (count($this->someCacheStore) >= $this->maxItems) {
                array_pop($this->someCacheStore);
            }

            $this->someCacheStore[$hash] = call_user_func_array(array($targetObject, $methodName), $arguments);
        }
        return $this->someCacheStore[$hash];
    }

}

// =====================================================================

$calculator = new SimpleCalculator();
$proxy = new CachingAroundProxy(5);

$factory = new StrictInterfaceProxyFactory();

/**
 * At this point 
 *  - Calculator instance is the way it always was. 
 *      It is unaware of caching (zero coupling).
 *      It never has to change.
 *  - CachingAroundProxy instance is generic.
 *      It does not know about caching calculator calls  (zero coupling).
 *      It's responsibility is to cache "some" calls to some objects. 
 *      It never has to change or extend anything.
 *      No one ever calls $proxy directly.
 */
$proxiedCalculator = $factory->createProxy($calculator, $proxy);

$calculator->add(3, 6); //9 slow
$calculator->add(3, 6); //9 slow

$proxiedCalculator->add(3, 6); //9 slow (cache miss)
$proxiedCalculator->add(3, 6); //9 fast (cache hit)
$proxiedCalculator->add(3, 1); //4 slow

/**
 * Now what PHP can not do is fulfill an interface this way so library would 
 * use tricks to allow this:
 */
($proxiedCalculator instanceof Calculator) == true;
($proxiedCalculator instanceof SlowCalculator) == true;
($proxiedCalculator instanceof CachingAroundProxy) == false;

function functionExpectingCalculator(Calculator $calc) {
    // do something
}

// $proxiedCalculator passes type hints too
functionExpectingCalculator($proxiedCalculator);


/**
 * We could aslo have Strict Interface Mode where we would extract only 
 * interfaces of an object and hide all non interface methods. 
 * Then $proxiedCalculator would not even pass type check for SlowCalculator as 
 * it would be a completly new class that would not even have rest of public 
 * methods that did not belong to interfaces.
 */