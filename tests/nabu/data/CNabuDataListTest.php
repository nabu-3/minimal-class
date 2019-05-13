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

use nabu\data\interfaces\INabuDataReadable;

/**
 * PHPUnit tests to verify functionality of class @see { CNabuDataList }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package nabu\data
 */
class CNabuDataListTest extends TestCase
{
    /**
     * @test __construct
     * @test count
     * @test current
     * @test next
     * @test key
     * @test valid
     * @test rewind
     * @test getMainIndexFieldName
     * @test isEmpty
     * @test isFilled
     * @test getKeys
     * @test getItems
     * @test addItem
     */
    public function testAddItem()
    {
        $arrobj = array();

        for ($i = 1; $i < 11; $i++) {
            $arrobj[$i - 1] = array(
                'key_field' => $i,
                'key_value' => "value $i"
            );
        }

        $list = new CNabuDataListTesting('key_field');
        $this->assertSame('key_field', $list->getMainIndexFieldName());
        $this->assertSame(0, count($list));
        $this->assertFalse($list->valid());
        $this->assertTrue($list->isEmpty());
        $this->assertFalse($list->isFilled());
        $this->assertNull($list->getKeys());
        $this->assertNull($list->getItems());

        $accumindex = array();

        for ($i = 1; $i <= count($arrobj); $i++) {
            $currarr = $arrobj[$i - 1];
            $accumindex[] = $currarr['key_field'];
            $payload = new CNabuDataListObjectTesting($currarr);
            $list->addItem($payload);
            $this->assertSame($i, count($list));
            $this->assertTrue($list->valid());
            $this->assertFalse($list->isEmpty());
            $this->assertTrue($list->isFilled());
            $this->assertSame($accumindex, $list->getKeys());
            $this->assertIsArray($list->getItems());
            $this->assertSame($i, count($list->getItems()));
            $this->assertTrue($list->hasKey($currarr['key_field']));
            $object = $list->getItem($i);
            $this->assertInstanceOf(INabuDataReadable::class, $object);
            $this->assertSame($currarr, $object->getValuesAsArray());
            if ($i === 1) {
                $list->rewind();
            } else {
                $list->next();
            }
            $this->assertSame($currarr, $list->current()->getValuesAsArray());
            $this->assertSame($currarr['key_field'], $list->key());
            $this->assertInstanceOf(INabuDataReadable::class, $list->current());
            $this->assertSame($payload, $list->current());
        }

        $list->next();
        $this->assertFalse($list->valid());
        $list->rewind();
        $this->assertTrue($list->valid());

        $item = $list->removeItem((int)(count($list) / 2));
        $this->assertInstanceOf(INabuDataReadable::class, $item);
        $this->assertSame(count($arrobj) - 1, count($list));
        $this->assertNull($list->getItem($item->getValue('key_field')));

        $item = $list->removeItem((int)(count($list) / 3));
        $this->assertInstanceOf(INabuDataReadable::class, $item);
        $this->assertSame(count($arrobj) - 2, count($list));
        $this->assertNull($list->getItem($item->getValue('key_field')));

        $list->clear();
        $this->assertSame(0, count($list));
        $this->assertFalse($list->valid());
        $this->assertTrue($list->isEmpty());
        $this->assertFalse($list->isFilled());
        $this->assertNull($list->getKeys());
        $this->assertNull($list->getItems());

        $this->assertNull($list->getItem(1));
    }
}

class CNabuDataListTesting extends CNabuDataList
{
    protected function acquireItem($key): ?INabuDataReadable
    {
        return null;
    }
}

class CNabuDataListObjectTesting extends CNabuDataObject
{

}
