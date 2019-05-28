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

use PHPUnit\Framework\Error\Error;

use PHPUnit\Framework\TestCase;

use nabu\data\interfaces\INabuDataReadable;
use nabu\data\interfaces\INabuDataListIndex;

/**
 * PHPUnit tests to verify functionality of class @see { CNabuAbstractDataIndexedList }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.4
 * @package nabu\data
 */
class CNabuAbstractDataIndexedListTest extends TestCase
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
     * @test nb_getMixedValue
     */
    public function testAssociativeList()
    {
        $arrobj = array();

        for ($i = 1; $i < 11; $i++) {
            $arrobj[$i - 1] = array(
                'key_field' => $i,
                'key_value' => "value $i",
                'key_value_2' => $i * 3
            );
        }

        $list = new CNabuAbstractDataIndexedListTesting('key_field');
        $this->assertSame('key_field', $list->getMainIndexFieldName());
        $this->assertSame(0, count($list));
        $this->assertFalse($list->valid());
        $this->assertTrue($list->isEmpty());
        $this->assertFalse($list->isFilled());
        $this->assertNull($list->getKeys());
        $this->assertNull($list->getItems());

        $accumindex = array();
        $accumsecond = array();

        for ($i = 1; $i <= count($arrobj); $i++) {
            $currarr = $arrobj[$i - 1];
            $accumindex[] = $currarr['key_field'];
            $accumsecond[$currarr['key_value']] = array(
                'key' => $currarr['key_value'],
                'pointer' => $currarr['key_field'],
                'order' => $currarr['key_value']
            );
            $payload = new CNabuAbstractDataIndexedListObjectTesting($currarr);
            $list->addItem($payload);
            $this->assertSame($i, count($list));
            $this->assertTrue($list->valid());
            $this->assertFalse($list->isEmpty());
            $this->assertTrue($list->isFilled());
            $this->assertSame($accumindex, $list->getKeys());
            $this->assertSame($accumsecond, $list->getKeys('secondary_index'));
            $this->assertIsArray($list->getItems());
            $this->assertSame($i, count($list->getItems()));
            $this->assertTrue($list->hasKey($currarr['key_field']));
            $this->assertTrue($list->hasKey($currarr['key_value'], 'secondary_index'));
            $object = $list->getItem($i);
            $this->assertInstanceOf(INabuDataReadable::class, $object);
            $this->assertSame($currarr, $object->getValuesAsArray());
            $object = $list->getItem($currarr['key_value'], 'secondary_index');
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

        $item = $list->removeItem($list->getItem((int)(count($list) / 3)));
        $this->assertInstanceOf(INabuDataReadable::class, $item);
        $this->assertSame(count($arrobj) - 2, count($list));
        $this->assertNull($list->getItem($item->getValue('key_field')));

        $index = $list->getSecondaryIndex('secondary_index_2');
        $this->assertInstanceOf(INabuDataListIndex::class, $index);
        $list->removeSecondaryIndex('secondary_index_2');

        $index = $list->getSecondaryIndex('secondary_index');
        $this->assertInstanceOf(INabuDataListIndex::class, $index);
        $list->removeSecondaryIndex('secondary_index');

        $list->clear();
        $this->assertSame(0, count($list));
        $this->assertFalse($list->valid());
        $this->assertTrue($list->isEmpty());
        $this->assertFalse($list->isFilled());
        $this->assertNull($list->getKeys());
        $this->assertNull($list->getItems());

        $this->assertNull($list->getItem(1));
    }

    /**
     * @test clear
     * @test getItem
     * @test getItemInternal
     */
    public function testClear()
    {
        $list = new CNabuAbstractDataIndexedListTesting('key_field');
        $object = $list->getItem('not present');
        $this->assertNull($object);
        $object = $list->getItem('not_pressent', 'secondary_index');
        $this->assertNull($object);
        $list->clear();
        $this->assertSame(0, count($list));
    }

    /**
     * @test getSecondaryIndex
     */
    public function testGetSecondaryIndexFails()
    {
        $list = new CNabuAbstractDataIndexedListTesting('key_field');
        $this->expectException(Error::class);
        $this->expectExceptionMessage(sprintf(TRIGGER_ERROR_INVALID_INDEX, 'secondary_index_3'));
        $list->getSecondaryIndex('secondary_index_3');
    }

    /**
     * @test removeSecondaryIndex
     */
    public function testRemoveSecondaryIndexFails()
    {
        $list = new CNabuAbstractDataIndexedListTesting('key_field');
        $this->expectException(Error::class);
        $this->expectExceptionMessage(sprintf(TRIGGER_ERROR_INVALID_INDEX, 'secondary_index_3'));
        $list->removeSecondaryIndex('secondary_index_3');
    }

    /**
     * @test merge
     */
    public function testMerge()
    {
        $list_left = new CNabuAbstractDataIndexedListTesting('key_field');
        for ($i = 1; $i < 21; $i = $i + 2) {
            $list_left->addItem(new CNabuAbstractDataIndexedListObjectTesting(
                array(
                    'key_field' => $i,
                    'key_value' => "value $i"
                )
            ));
        }

        $list_right = new CNabuAbstractDataIndexedListTesting('key_field');
        for ($i = 2; $i < 22; $i = $i + 2) {
            $list_right->addItem(new CNabuAbstractDataIndexedListObjectTesting(
                array(
                    'key_field' => $i,
                    'key_value' => "value $i"
                )
            ));
        }

        $merge_list = new CNabuAbstractDataIndexedListTesting('key_field');
        $merge_list->merge($list_left);
        $merge_list->merge($list_right);

        $this->assertSame(20, count($merge_list));
    }
}

class CNabuAbstractDataIndexedListTesting extends CNabuAbstractDataIndexedList
{
    protected function acquireItem($key, ?string $index = null): ?INabuDataReadable
    {
        return null;
    }

    protected function createSecondaryIndexes(): void
    {
        $this->addSecondaryIndex(
            new CNabuAbstractDataIndexedListIndex($this, 'key_value', 'key_value', 'secondary_index')
        );
        $this->addSecondaryIndex(
            new CNabuAbstractDataIndexedListIndex($this, 'key_value_2', 'key_value_2', 'secondary_index_2')
        );
    }

    protected function createDataInstance(array $data): ?INabuDataReadable
    {
        return new CNabuAbstractDataIndexedListObjectTesting($data);
    }
}

class CNabuAbstractDataIndexedListObjectTesting extends CNabuAbstractDataObject
{

}
