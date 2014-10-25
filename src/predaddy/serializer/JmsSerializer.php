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

namespace predaddy\serializer;

use JMS\Serializer\SerializerInterface;
use precore\lang\ObjectClass;
use precore\lang\ObjectInterface;

final class JmsSerializer implements Serializer
{
    /**
     * @var SerializerInterface
     */
    private $jmsSerializer;

    /**
     * @var string
     */
    private $format;

    /**
     * @param SerializerInterface $jmsSerializer
     * @param string $format xml|json|yaml
     */
    public function __construct(SerializerInterface $jmsSerializer, $format)
    {
        $this->jmsSerializer = $jmsSerializer;
        $this->format = $format;
    }

    /**
     * @param ObjectInterface $object
     * @return string
     */
    public function serialize(ObjectInterface $object)
    {
        return $this->jmsSerializer->serialize($object, $this->format);
    }

    /**
     * @param string $serialized
     * @param ObjectClass $outputClass
     * @return ObjectInterface
     */
    public function deserialize($serialized, ObjectClass $outputClass)
    {
        return $this->jmsSerializer->deserialize($serialized, $outputClass->getName(), $this->format);
    }
}
