<?php
/*
 * Copyright (c) 2013 Janos Szurovecz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace predaddy\messagehandling\mf4php;

use InvalidArgumentException;
use mf4php\DefaultQueue;
use mf4php\Message as Mf4phpMessage;
use mf4php\MessageDispatcher;
use mf4php\MessageListener;
use mf4php\ObjectMessage;
use precore\lang\NullPointerException;
use precore\util\Preconditions;
use predaddy\messagehandling\MessageCallback;
use predaddy\messagehandling\SimpleMessageBus;
use Serializable;

/**
 * {@link MessageBus} implementation which uses mf4php to forward messages.
 *
 * If you use a proper MessageDispatcher it is possible to
 * handle messages after the transaction has been committed.
 *
 * With an asynchronous {@link MessageDispatcher} implementation message
 * handling can be asynchronous.
 *
 * Do not register it to the given {@link MessageDispatcher},
 * it is handled by default.
 *
 * Does not support {@link MessageCallback}!
 *
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
class Mf4PhpMessageBus extends SimpleMessageBus implements MessageListener
{
    private $dispatcher;
    private $queue;
    private $objectMessageFactories = [];
    private $defaultObjectMessageFactory;

    /**
     * @param Mf4PhpMessageBusBuilder $builder
     */
    public function __construct(Mf4PhpMessageBusBuilder $builder)
    {
        parent::__construct($builder);
        $this->dispatcher = $builder->getDispatcher();
        $this->queue = new DefaultQueue($builder->getIdentifier());
        $this->defaultObjectMessageFactory = new DefaultObjectMessageFactory();
        $this->dispatcher->addListener($this->queue, $this);
    }

    /**
     * @param MessageDispatcher $dispatcher the default value is due to PHP restrictions
     * @return Mf4PhpMessageBusBuilder
     * @throws NullPointerException if $dispatcher is null
     */
    public static function builder(MessageDispatcher $dispatcher = null)
    {
        return new Mf4PhpMessageBusBuilder(Preconditions::checkNotNull($dispatcher));
    }

    /**
     * Only full matching class names are used, you should not register
     * a factory for an abstract message class/interface!
     *
     * If there is no registered factory for a particular message class,
     * DefaultObjectMessageFactory will be used.
     *
     * @param ObjectMessageFactory $factory
     * @param $messageClass
     */
    public function registerObjectMessageFactory(ObjectMessageFactory $factory, $messageClass)
    {
        $this->objectMessageFactories[$messageClass] = $factory;
    }

    /**
     * Forward incoming message to handlers.
     *
     * @param Mf4phpMessage $message
     * @throws InvalidArgumentException
     */
    public function onMessage(Mf4phpMessage $message)
    {
        Preconditions::checkArgument($message instanceof ObjectMessage, "Message must be an instance of ObjectMessage");
        $object = $message->getObject();
        if ($object instanceof MessageWrapper) {
            $object = $object->getMessage();
        }
        parent::dispatch($object, self::emptyCallback());
    }

    /**
     * Finds the appropriate message factory for the given message.
     *
     * @param $message
     * @return ObjectMessageFactory
     */
    protected function findObjectMessageFactory($message)
    {
        $messageClass = get_class($message);
        foreach ($this->objectMessageFactories as $class => $factory) {
            if ($class === $messageClass) {
                return $factory;
            }
        }
        return $this->defaultObjectMessageFactory;
    }

    /**
     * Send the message to the message queue.
     *
     * @param $message
     * @param MessageCallback $callback
     */
    protected function dispatch($message, MessageCallback $callback)
    {
        $sendable = $message;
        if (!($message instanceof Serializable)) {
            $sendable = new MessageWrapper($message);
        }
        $mf4phpMessage = $this->findObjectMessageFactory($message)->createMessage($sendable);
        $this->dispatcher->send($this->queue, $mf4phpMessage);
    }
}
