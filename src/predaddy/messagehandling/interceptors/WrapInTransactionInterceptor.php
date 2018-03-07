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

namespace predaddy\messagehandling\interceptors;

use Exception;
use lf4php\MDC;
use precore\lang\Obj;
use precore\util\UUID;
use predaddy\messagehandling\DispatchInterceptor;
use predaddy\messagehandling\InterceptorChain;
use predaddy\messagehandling\SubscriberExceptionContext;
use predaddy\messagehandling\SubscriberExceptionHandler;
use trf4php\TransactionManager;

/**
 * Wraps the command dispatch process in transaction. It handles nested levels, which means it does not
 * start a new transaction if there is an open one.
 *
 * <p> It puts a transaction ID to MDC with 'TR' key.
 *
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
final class WrapInTransactionInterceptor extends Obj implements DispatchInterceptor, SubscriberExceptionHandler
{
    const MDC_KEY = 'TR';

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    private $transactionLevel = 0;

    private $rollbackRequired = false;

    /**
     * @param TransactionManager $transactionManager
     */
    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function invoke($message, InterceptorChain $chain)
    {
        try {
            $this->transactionLevel++;
            if ($this->transactionLevel == 1) {
                $this->transactionManager->beginTransaction();
                MDC::put(self::MDC_KEY, UUID::randomUUID()->toString());
                self::getLogger()->debug('Transaction has been started');
                $chain->proceed();
                if ($this->rollbackRequired) {
                    $this->transactionManager->rollback();
                    $this->rollbackRequired = false;
                    self::getLogger()->debug('Transaction has been rolled back');
                } else {
                    $this->transactionManager->commit();
                    self::getLogger()->debug('Transaction has been committed');
                }
                MDC::remove(self::MDC_KEY);
            } else {
                self::getLogger()->debug('Transaction has already started, using the existing one');
                $chain->proceed();
            }
            $this->transactionLevel--;
        } catch (Exception $e) {
            MDC::remove(self::MDC_KEY);
            $this->transactionLevel--;
            throw $e;
        }
    }

    public function handleException(Exception $exception, SubscriberExceptionContext $context)
    {
        $this->rollbackRequired = true;
        self::getLogger()->debug("Transaction rollback required, context '{}'!", [$context], $exception);
    }

    /**
     * @return int
     */
    public function getTransactionLevel()
    {
        return $this->transactionLevel;
    }
}
