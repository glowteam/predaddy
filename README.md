predaddy
========
[![Latest Stable Version](https://poser.pugx.org/predaddy/predaddy/v/stable.png)](https://packagist.org/packages/predaddy/predaddy)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/szjani/predaddy/badges/quality-score.png?s=496589a983254d22b4334552572b833061b9bd03)](https://scrutinizer-ci.com/g/szjani/predaddy/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ad36fc7a-f48d-4919-b20d-90eae34aecd9/mini.png)](https://insight.sensiolabs.com/projects/ad36fc7a-f48d-4919-b20d-90eae34aecd9)
[![Gitter chat](https://badges.gitter.im/szjani/predaddy.png)](https://gitter.im/szjani/predaddy)

|master|1.2|2.0|
|------|---|---|
|[![Build Status](https://travis-ci.org/szjani/predaddy.png?branch=master)](https://travis-ci.org/szjani/predaddy)|[![Build Status](https://travis-ci.org/szjani/predaddy.png?branch=1.2)](https://travis-ci.org/szjani/predaddy)| [![Build Status](https://travis-ci.org/szjani/predaddy.png?branch=2.0)](https://travis-ci.org/szjani/predaddy)|
|[![Coverage Status](https://coveralls.io/repos/szjani/predaddy/badge.png?branch=master)](https://coveralls.io/r/szjani/predaddy?branch=master)|[![Coverage Status](https://coveralls.io/repos/szjani/predaddy/badge.png?branch=1.2)](https://coveralls.io/r/szjani/predaddy?branch=1.2)|[![Coverage Status](https://coveralls.io/repos/szjani/predaddy/badge.png?branch=2.0)](https://coveralls.io/r/szjani/predaddy?branch=2.0)|

It is a library which gives you some usable classes to be able to use common DDD patterns. Predaddy components can be used in any projects regardless of the fact that you are using DDD or not.
You can find some examples in the [sample directory](https://github.com/szjani/predaddy/tree/master/sample).

Predaddy uses [lf4php](https://github.com/szjani/lf4php) for logging.

Components
----------

History
-------

### 2.0

 - The constructors are changed in all message bus implementations since they use the same `FunctionDescriptorFactory` for closures as the `MessageHandlerDescriptorFactory` for methods.
 - `TransactionSynchronizedBuffererInterceptor` has been introduced thus `EventBus` does not extends `Mf4PhpMessageBus` anymore.
 - Both `EventBus` and `CommandBus` now register theirs default interceptors in the constructor and do not override the `setInterceptors` method. If you want to register other interceptors,
 it's your responsible to register all default interceptors if required.

### 1.2

#### Event handling has been refactored

Classes have been moved to a different namespace, you have to modify `use` your statements.

#### Interceptor support added

Take a look at [TransactionInterceptor](https://github.com/szjani/predaddy/blob/1.2/src/predaddy/messagehandling/interceptors/TransactionInterceptor.php).

#### No more marker interface for handler classes

Handler classes do not have to implement any interfaces. You have to remove `implements EventHandler` parts from your classes.

#### MessageCallback added

You can register a callback class for a message post to be able to catch the result.

### 1.1

#### Allowed private variables in events

Until now you had to override `serialize()` and `unserialize()` methods if you added any private members to your event classes even if you extended `EventBase`.
Serialization now uses reflection which might have small performance drawback but it is quite handy. So if you extend EventBase, you can forget this problem.

#### Closures are supported

Useful in unit tests or in simple cases.

```php
$closure = function (Event $event) {
    echo 'will be called with all events';
};
$bus->registerClosure($closure);
$bus->post(new SimpleEvent());
