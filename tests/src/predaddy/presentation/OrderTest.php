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

namespace predaddy\presentation;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $property = 'property';
        $order = new Order(Direction::$ASC, $property);
        self::assertTrue($order->getDirection()->equals(Direction::$ASC));
    }

    public function testEquals()
    {
        $property = 'property';
        $order = new Order(Direction::$ASC, $property);

        $order2 = new Order(Direction::$ASC, $property);
        self::assertTrue($order->equals($order));
        self::assertTrue($order->equals($order2));

        self::assertFalse($order->equals(null));
    }

    public function testToString()
    {
        $property = 'name';
        $order = new Order(Direction::$ASC, $property);
        self::assertEquals(
            'predaddy\presentation\Order{name, ASC}',
            $order->toString()
        );
    }
}
