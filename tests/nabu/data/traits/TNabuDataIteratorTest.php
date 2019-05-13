<?php

/** @license
 *  Copyright 2009-2011 Rafael Gutierrez Martinez
 *  Copyright 2012-2013 Welma WEB MKT LABS, S.L.
 *  Copyright 2014-2016 Where Ideas Simply Come True, S.L.
 *  Copyright 2017 nabu-3 Group
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace nabu\data\traits;

use Iterator;
use Exception;

use PHPUnit\Framework\TestCase;

use nabu\data\CNabuRODataObject;

/**
 * PHPUnit tests to verify functionality of class @see { TNabuDataIterator }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.3
 * @package nabu\data\traits
 */
class TNabuDataIteratorTest extends TestCase
{
    /**
     * @test current
     * @test valid
     * @test next
     * @test rewind
     * @test key
     */
    public function testIteratorInterface()
    {
        $initial = array(
            "a" => 3,
            "b" => 10,
            "c" => 4,
            "d" => 8,
            "f" => null
        );

        $object = new CNabuDataIteratorTesting($initial);
        $this->assertSame($initial, $object->getValuesAsArray());

        $iterated = array();

        $object->rewind();
        $i = 0;
        foreach ($initial as $key => $value) {
            $currval = $object->current();
            $currkey = $object->key();
            $this->assertSame($key, $currkey);
            $this->assertSame($value, $currval);
            $this->assertTrue($object->valid());
            $object->next();
        }
        $this->assertFalse($object->valid());
    }

    /**
     * @test current
     * @test valid
     * @test next
     * @test rewind
     * @test key
     */
    public function testIteratorROEmpty()
    {
        $object = new CNabuDataIteratorTesting();

        $this->assertFalse($object->valid());
        $object->rewind();
        $this->assertNull($object->current());
        $this->assertNull($object->key());
        $object->next();

        foreach ($object as $key => $value) {
            throw new Exception('Invalid interation');
        }
    }

    /**
     * @test current
     * @test valid
     * @test next
     * @test rewind
     * @test key
     */
    public function testIteratorRO()
    {
        $initial = array(
            "a" => 3,
            "b" => 10,
            "c" => 4,
            "d" => 8,
            "f" => null
        );

        $object = new CNabuDataIteratorTesting($initial);
        $this->assertSame($initial, $object->getValuesAsArray());

        $iterated = array();

        foreach ($initial as $key => $value) {
            $iterated[$key] = $value;
        }

        $this->assertSame($initial, $iterated);
    }
}

class CNabuDataIteratorTesting extends CNabuRODataObject implements Iterator
{
    use TNabuDataIterator;
}
