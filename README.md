# php-proxy

Library allowing you to build proxy instances at runtime. Similar to AOP concepts.

## Features

1. Create proxy instances at runtime. 
2. Proxy instance passes all type checks of original obejct.
3. Proxy instance delegates all or selected methods to the original object.
4. 100% decoupled code, proxy does not know what is proxied, target does not know that it is proxied.

## Notes

1. Proxied object is not modified in any way. 
2. You can proxy any existing PHP object.
3. Proxied object can invoke it's own methods and these calls do not go throught the proxy. 
    This can be considered a good or a bad thing depending on how you look at it. It is a problem if you wanted to 
    selectively proxy methods for something like security. You could imagine developer changing the underlying code
    and delegating from less secure method to more secure method and proxy would not have a chance to intercept that.
    On the other hand you have full transparency of what happens and library is very lightweight, it does not modify existing code.
4. If you want strict AOP you might want to consider class-load-time-weaving, which modifies class when it is loaded for the first time.
    Then you can get full method interception as instances of class created have proxy baked-in.
    Have a look at https://github.com/lisachenko/go-aop-php as it looks promising (not sure if it is production ready)
5. Performance should not be affected significantly by this construct as we would generate classes and write them to cache files.
    Class could be cached in APC (or other byte code cache) and reused after initial run.
6. Diagram of objects would look like this (where <> represents composition, has a reference).

        proxyImplementationInstance-----<>generatedProxyInstance<>-----targetInstance

    targetInstance and proxyImplementationInstance are "owned" by the generatedProxyInstance. 
    generatedProxyInstance routes method calls to proxyImplementationInstance.
    proxyImplementationInstance can decide when and how to delegate to targetInstance (as in CachingAroundProxy)
    

## Example use

    <?php
    // real instances of target and "aspect" proxy
    $calculator = new SimpleCalculator();
    $proxy = new CachingAroundProxy(5);

    // get proxied instance
    $factory = new StrictInterfaceProxyFactory();
    $proxiedCalculator = $factory->createProxy($calculator, $proxy);

    // call target directly (proxy does not get called)
    $calculator->add(3, 6); //9 slow
    $calculator->add(3, 6); //9 slow

    // call target via proxy
    $proxiedCalculator->add(3, 6); //9 slow (cache miss)
    $proxiedCalculator->add(3, 6); //9 fast (cache hit)
    $proxiedCalculator->add(3, 1); //4 slow

    // type checks
    ($proxiedCalculator instanceof Calculator) == true;
    ($proxiedCalculator instanceof SlowCalculator) == true;
    ($proxiedCalculator instanceof CachingAroundProxy) == false;

## More examples

Please have a look at this php file added. It contains a pseudo-code of target, proxy, and a few interfaces.
It shows how generator would be used and what would it allow you to do.

Warning - code in that file is a hack / sample i have not ran it and it is not complete.

https://github.com/ejsmont-artur/phpProxyBuilder/blob/master/useCases.php

## Running the tests

Tests are run via PHPUnit It is assumed to be installed via PEAR.

From the root of the repository

    phpunit --bootstrap php-unit-bootstrap.php Proxy/Tests/Unit

## Authors

* Artur Esjmont (https://github.com/ejsmont-artur)
* Shawn Murphy (https://github.com/seguer)