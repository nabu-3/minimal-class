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

namespace nabu\data;

use PHPUnit\Framework\TestCase;

use nabu\data\interfaces\INabuDataIterable;

/**
 * PHPUnit tests to verify functionality of class @see { CNabuDataIterable }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.4
 * @version 3.0.4
 * @package nabu\data
 */
class CNabuDataIterableTest extends TestCase
{
    /**
     * @test __construct
     * @test count
     * @test valid
     * @test isEmpty
     * @test isFilled
     */
    public function testEmptyIterable()
    {
        $list = new CNabuDataIterableTesting();
        $this->assertSame(0, count($list));
        $this->assertSame(0, $list->count());
        $this->assertFalse($list->valid());
        $this->assertTrue($list->isEmpty());
        $this->assertFalse($list->isFilled());
    }

    /**
     * @test __construct
     * @test count
     * @test current
     * @test next
     * @test key
     * @test valid
     * @test rewind
     * @test clearInternal
     * @test isEmpty
     * @test isFilled
     * @test nb_getMixedValue
     */
    public function testFilledIterable()
    {
        $arrobj = array();
        $limit = 10;

        for ($i = 1; $i <= $limit; $i++) {
            $arrobj["key_$i"] = "value $i";
        }

        $list = new CNabuDataIterableTesting($arrobj);
        $this->assertSame($limit, count($list));
        $this->assertSame($limit, $list->count());
        $this->assertTrue($list->valid());
        $this->assertFalse($list->isEmpty());
        $this->assertTrue($list->isFilled());

        $list->rewind();

        for ($i = 1; $i <= $limit; $i++) {
            $this->assertTrue($list->valid());
            $this->assertSame($arrobj["key_$i"], $list->current());
            $this->assertSame("key_$i", $list->key());
            $this->assertSame("value $i", $list->current());
            $list->next();
        }

        $this->assertFalse($list->valid());
        $list->rewind();
        $this->assertTrue($list->valid());

        for ($i = 1; $i < $limit * 10; $i++) {
            $j = rand(1, $limit);
            $key = "key_$j";
            $value = "value $j";
            $list->seek($j - 1);
            $this->assertTrue($list->valid());
            $this->assertSame($key, $list->key());
            $this->assertSame($value, $list->current());
        }

        $i = (int)(count($list) / 2);

        $list->clear();
        $this->assertSame(0, count($list));
        $this->assertFalse($list->valid());
        $this->assertTrue($list->isEmpty());
        $this->assertFalse($list->isFilled());

        $this->assertNull($list->current());
    }
}

class CNabuDataIterableTesting extends CNabuDataIterable
{
    public function clear(): INabuDataIterable
    {
        $this->clearInternal();

        return $this;
    }
}
