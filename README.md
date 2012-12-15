phpProxyBuilder
===============

Library allowing you to build proxy instances at runtime. Similar to AOP concepts.

## Features ##

1. Create prox instances at runtime. 
2. Proxy instance passes all type checks of original obejct.
3. Proxy instance delegates all or selected methods to the original object.
4. 100% decoupled code, proxy does not know what is proxied, target does not know that it is proxied.

## Clinet code interactions ##

    <?php

    $calculator = new SimpleCalculator();
    $proxy = new CachingAroundProxy(5);

    $factory = new StrictInterfaceProxyFactory();

    $proxiedCalculator = $factory->createProxy($calculator, $proxy);

    $calculator->add(3, 6); //9 slow
    $calculator->add(3, 6); //9 slow

    $proxiedCalculator->add(3, 6); //9 slow (cache miss)
    $proxiedCalculator->add(3, 6); //9 fast (cache hit)
    $proxiedCalculator->add(3, 1); //4 slow

    ($proxiedCalculator instanceof Calculator) == true;
    ($proxiedCalculator instanceof SlowCalculator) == true;
    ($proxiedCalculator instanceof CachingAroundProxy) == false;

## More examples ##


