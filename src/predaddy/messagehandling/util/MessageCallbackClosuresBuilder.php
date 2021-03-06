<?php
/*
 * Copyright (c) 2012-2014 Janos Szurovecz
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

namespace predaddy\messagehandling\util;

use Closure;

/**
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
final class MessageCallbackClosuresBuilder
{
    /**
     * @var Closure|null
     */
    protected $successClosure;

    /**
     * @var Closure|null
     */
    protected $failureClosure;

    /**
     * @param callable $closure
     * @return MessageCallbackClosuresBuilder $this
     */
    public function successClosure(Closure $closure)
    {
        $this->successClosure = $closure;
        return $this;
    }

    /**
     * @param callable $closure
     * @return MessageCallbackClosuresBuilder $this
     */
    public function failureClosure(Closure $closure)
    {
        $this->failureClosure = $closure;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getFailureClosure()
    {
        return $this->failureClosure;
    }

    /**
     * @return callable|null
     */
    public function getSuccessClosure()
    {
        return $this->successClosure;
    }

    /**
     * @return MessageCallbackClosures
     */
    public function build()
    {
        return MessageCallbackClosures::build($this);
    }
}
