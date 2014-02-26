MessageBus
----------

MessageBus provides a general interface for message handling. The basic concept is that message handlers can
be registered to the bus which forwards each incoming messages to the appropriate handler. Message handlers
can be either objects or closures.

SimpleMessageBus is a basic implementation of the MessageBus interface. Currently, all other MessageBus implementations extend this class.

If you use CQRS, then I highly recommend to use the pre-configured `EventBus` and `CommandBus` classes.

### Handler methods/functions

Predaddy is quite configurable, but it has several default behaviours. Handler functions/methods should have one parameter with typehint.
The typehint defines which `Message` objects can be handled, by default. If you want to handle all `Message` objects,
you just have to use `Message` typehint. This kind of solution provides an easy way to use and distinguish a huge amount of
message classes. Interface and abstract class typehints also work as expected.

### Annotations

You can use your own handler method scanning/defining process, however the system does support annotation based configuration.
It means that you just have to mark handler methods in your handler classes with `@Subject` annotation. When you register an instance
of this class, predaddy is automatically finding these methods.

### Interceptors

It's possible to extend bus behaviour when messages are being dispatched to message handlers. `HandlerInterceptor` objects wrap
the concrete dispatch process and are able to modify that. It is usable for logging, transactions, etc.

There are two builtin interceptors:

 - `WrapInTransactionInterceptor`: All message dispatch processes will be wrapped in a new transaction.
 - `TransactionSynchronizedBuffererInterceptor`: Message dispatching is synchronized to transactions which means that if there is an already started transaction
 then messages are being buffered until the transaction is being committed. If the transaction is not successful then buffer is being cleared after rollback without sending out any messages.
 Without an already started transaction buffering is disabled.

### Unhandled messages

Those messages which haven't been sent to any handlers during the dispatch process are being wrapped into an individual `DeadMessage` object and being dispatched again.
Registering a `DeadMessage` handler is a common way to log or debug unhandled messages.

### MessageBus implementations

There is a default implementation of `MessageBus` interface called `SimpleMessageBus`. Predaddy also provides some other specialized implementations which extend this class.

#### CommandBus

`WrapInTransactionInterceptor` is registered which indicates that all command handlers are wrapped in a unique transaction.
`Message` objects must implement `Command` interface. The typehint in the handler methods must be exactly the same as the command object's type.

#### EventBus

`TransactionSynchronizedBuffererInterceptor` is registered which means that event handlers are being called only after a the transaction has been successfully committed.
This message bus implementation uses the default typehint handling (subclass handling, etc.). Message objects
must implement `Event` interface. Messages are buffered until the transaction is committed.

#### Mf4phpMessageBus

`Mf4phpMessageBus` wraps a `MessageDispatcher`, so all features provided by [mf4php](https://github.com/szjani/mf4php) can be achieved with this class, such as
synchronize messages to transactions and asynchronous event dispatching. For further information see the [mf4php](https://github.com/szjani/mf4php) documentation.