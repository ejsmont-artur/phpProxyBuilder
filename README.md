# phpProxyBuilder

Library allowing you to add proxy objects around arvitrary class instances to add behaviour at runtime. 

Library employs concepts of Aspect Oriented Programming where a certain logic (like caching) is reused across the
application without coupling the application code to the caching implementation.

## Key Features

1. Create proxy instances at runtime. 
2. Proxy uses an instance of Advice (for example CachingAdvice) to add behaviour to the proxied object.
3. Proxy instance delegates method calls to the original (proxied) object.
4. Proxy, Advice and proxied class are 100% decoupled. Proxy does not know what is proxied nor what is the 
advice's purpose. Advice does not care about how it is being used and it can be used for any target class.
Finally and most importantly client code does not have to know that any proxy exists. It consumes proxy as if it was
the target instance itself. Proxy is transparent to the client.

## Example

    <?php
    // target instances to be proxied
    $target = new SlowService();

    // Advice implementing additional logic.
    // We will integrate with frameworks to obtain $cacheBackendAdapter from your framework of choice like symfony2.
    $advice = new CachingAdvice($cacheBackendAdapter);

    $factory = new MagicMethodBasedFactory();
    $proxiedService = $factory->createProxy($advice, $target);

    // Call is made directly on the object, it's slow.
    $target->getSomeData(12345);

    // Calls are made through the proxy, which delegates to the CachingAdvice and then to target instance.
    // First call is slow, the second call is fast as it is being cached.
    $proxiedService->getSomeData(12345);
    $proxiedService->getSomeData(12345);

## Future Implementations

The code you can see now has full test coverage but it is just the first implementation of the library.

Current implementation is based on magic methods. Because of this fact proxy does not inherit nor extend the 
proxied object so it does not pass type hint checks nor instanceof checks.

In near future we will provide a second implementation that will allow you to create a full interface based proxy
from any object. This way proxy will be fully interchangable with the target instance and will pass type checks like:

    <?php
    
    ($proxiedService instanceof SlowService) == true;
    ($proxiedService instanceof CachingAdvice) == false;

## More examples

Please have a look at unit tests to see more examples of how the code should be used and assembled.

## Included Advices

Library gains a lot of value by providing simple yet useful implementations of advices. We decided to 
keep all external dependencies out of the core of the library so we do not have logger nor cache implementations.
We will integrate with frameworks to provide implementations of our minimalistic interfaces stored in Adapter folder.

1. CachingAdvice - adds caching to arbitrary object. Uses class name, method name and arguments as cache key.
2. CircuitBreakerAdvice - adds circuit breaker around an object to fail fast in times of unavailability.
3. InstrumentationAdvice - counts method calls and measures execution times for monitoring and graphing.
4. ClosureAdvice - lets you use a closure instead of full AroundAdviceInterface implementation.
5. LoggingAdvice - logs all method calls with times and optionally arguments/results.

## Notes

1. Proxied object is not modified in any way, it is just being used. 
2. You can proxy any existing PHP object.
3. Proxied object can invoke it's own methods and these calls do not go throught the proxy. 
    This can be considered a good or a bad thing depending on how you look at it. It is a problem if you wanted to 
    selectively proxy methods for something like security. You could imagine developer changing the underlying code
    and delegating from less secure method to more secure method and proxy would not have a chance to intercept that.
    On the other hand you have full transparency of what happens and library is very lightweight, it does not modify existing code.
4. If you want strict AOP you might want to consider class-load-time-weaving, which modifies class when it is loaded for the first time.
    Then you can get full method interception as instances of class created have proxy baked-in.
    Have a look at https://github.com/lisachenko/go-aop-php as it looks promising (not sure if it is production ready)
5. Performance is an imporant factor and we will provide benchmarks and make sure code does not slow down the overall
    execution.
6. Diagram of objects would look like this:

        proxyImplementationInstance-----<>generatedProxyInstance<>-----targetInstance

    targetInstance and proxyImplementationInstance are "owned" by the generatedProxyInstance. 
    generatedProxyInstance routes method calls to proxyImplementationInstance.
    proxyImplementationInstance can decide when and how to delegate to targetInstance (as in CachingAroundProxy)

## Running tests

Tests are run via PHPUnit and it is assumed that you have phpunit installed if you want to run tests.
You can run tests using ant, "ci" target generates documentation and code coverage report. 

You can run all tests using any of the following commands:

    ant
    ant phpunit
    ant ci

You can run selected test case by running:

    cd tests
    phpunit Unit/PhpProxyBuilder/Aop/ClosureAdviceTest.php

## Contributors

* Artur Esjmont (https://github.com/ejsmont-artur)
* Shawn Murphy (https://github.com/seguer)
