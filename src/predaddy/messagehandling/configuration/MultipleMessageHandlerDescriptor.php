<?php
/*
 * Copyright (c) 2014 Janos Szurovecz
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

namespace predaddy\messagehandling\configuration;

use predaddy\messagehandling\FunctionDescriptor;
use predaddy\messagehandling\MessageHandlerDescriptor;

/**
 * Merges {@link FunctionDescriptor}s returned by the given {@link MessageHandlerDescriptor}s.
 *
 * @package predaddy\messagehandling\configuration
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
final class MultipleMessageHandlerDescriptor implements MessageHandlerDescriptor
{
    /**
     * @var MessageHandlerDescriptor[]
     */
    private $handlerDescriptors;

    /**
     * @var FunctionDescriptor[]
     */
    private $functionDescriptors = null;

    /**
     * @param MessageHandlerDescriptor[] $handlerDescriptors
     */
    public function __construct(array $handlerDescriptors)
    {
        $this->handlerDescriptors = $handlerDescriptors;
    }

    /**
     * @return FunctionDescriptor[]
     */
    public function getFunctionDescriptors()
    {
        if ($this->functionDescriptors === null) {
            $this->functionDescriptors = [];
            foreach ($this->handlerDescriptors as $handlerDescriptor) {
                foreach ($handlerDescriptor->getFunctionDescriptors() as $innerFuncDescriptor) {
                    $this->addIfDoesNotContain($innerFuncDescriptor);
                }
            }
        }
        return $this->functionDescriptors;
    }

    private function addIfDoesNotContain(FunctionDescriptor $functionDescriptor)
    {
        foreach ($this->functionDescriptors as $currentDescriptor) {
            if ($currentDescriptor->equals($functionDescriptor)) {
                return;
            }
        }
        $this->functionDescriptors[] = $functionDescriptor;
    }
}
