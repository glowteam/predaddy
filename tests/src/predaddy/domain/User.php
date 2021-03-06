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

namespace predaddy\domain;

use precore\lang\ObjectInterface;
use precore\util\Preconditions;
use precore\util\UUID;
use predaddy\messagehandling\annotation\Subscribe;

/**
 * Description of User
 *
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
class User extends AbstractAggregateRoot
{
    const DEFAULT_VALUE = 1;

    /**
     * @var UUIDAggregateId
     */
    private $id;

    /**
     * @var int
     */
    public $value = self::DEFAULT_VALUE;

    /**
     * @var int
     */
    private $version = 0;

    public function __construct()
    {
        $this->id = UUID::randomUUID()->toString();
        $this->raise(new UserCreated());
    }

    public function getId()
    {
        return new GenericAggregateId($this->id, self::className());
    }

    public function increment()
    {
        Preconditions::checkState($this->value <= 2, "Cannot be incremented more, than twice");
        $this->value++;
        $this->version++;
        $this->raise(new IncrementedEvent());
    }
}
