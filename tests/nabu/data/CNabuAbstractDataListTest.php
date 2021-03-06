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

use InvalidArgumentException;
use UnexpectedValueException;

use PHPUnit\Framework\Error\Error;

use PHPUnit\Framework\TestCase;

use nabu\data\interfaces\INabuDataReadable;

/**
 * PHPUnit tests to verify functionality of class @see { CNabuAbstractDataList }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.4
 * @package nabu\data
 */
class CNabuAbstractDataListTest extends TestCase
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
    public function testArrayList()
    {
        $arrobj = array();

        for ($i = 1; $i < 11; $i++) {
            $arrobj[$i - 1] = array(
                'key_field' => $i,
                'key_value' => "value $i"
            );
        }

        $list = new CNabuAbstractDataListTesting();
        $this->assertNull($list->getMainIndexFieldName());
        $this->assertSame(0, count($list));
        $this->assertFalse($list->valid());
        $this->assertTrue($list->isEmpty());
        $this->assertFalse($list->isFilled());
        $this->assertNull($list->getKeys());
        $this->assertNull($list->getItems());

        $accumindex = array();

        for ($i = 1; $i <= count($arrobj); $i++) {
            $currarr = $arrobj[$i - 1];
            $accumindex[] = ($i - 1);
            $payload = new CNabuAbstractDataListObjectTesting($currarr);
            $list->addItem($payload, $i - 1);
            $this->assertSame($i, count($list));
            $this->assertTrue($list->valid());
            $this->assertFalse($list->isEmpty());
            $this->assertTrue($list->isFilled());
            $this->assertSame($accumindex, $list->getKeys());
            $this->assertIsArray($list->getItems());
            $this->assertSame($i, count($list->getItems()));
            $this->assertTrue($list->hasKey($i - 1));
            $object = $list->getItem($i - 1);
            $this->assertInstanceOf(INabuDataReadable::class, $object);
            $this->assertSame($currarr, $object->getValuesAsArray());
            if ($i === 1) {
                $list->rewind();
            } else {
                $list->next();
            }
            $this->assertSame($currarr, $list->current()->getValuesAsArray());
            $this->assertSame($i - 1, $list->key());
            $this->assertInstanceOf(INabuDataReadable::class, $list->current());
            $this->assertSame($payload, $list->current());
        }

        $list->next();
        $this->assertFalse($list->valid());
        $list->rewind();
        $this->assertTrue($list->valid());

        $i = (int)(count($list) / 2);
        $item = $list->removeItem($i);
        $this->assertInstanceOf(INabuDataReadable::class, $item);
        $this->assertSame(count($arrobj) - 1, count($list));
        $this->assertNull($list->getItem($i));

        $i = (int)(count($list) / 3);
        $item = $list->removeItem($list->getItem($i));
        $this->assertInstanceOf(INabuDataReadable::class, $item);
        $this->assertSame(count($arrobj) - 2, count($list));
        $this->assertNull($list->getItem($i));

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
                'key_value' => "value $i"
            );
        }

        $list = new CNabuAbstractDataListTesting('key_field');
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
            $payload = new CNabuAbstractDataListObjectTesting($currarr);
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

        $item = $list->removeItem($list->getItem((int)(count($list) / 3)));
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

    /**
     * @test hasKey
     */
    public function testHasKeyFails()
    {
        $list = new CNabuAbstractDataListTesting();

        $this->expectException(Error::class);
        $this->expectExceptionMessage(sprintf(TRIGGER_ERROR_INVALID_KEY, var_export(array('check_key'), true)));
        $list->hasKey(array('check_key'));
    }

    /**
     * @test createItemFromArray
     */
    public function testCreateItemFromArray()
    {
        $list = new CNabuAbstractDataListTesting('key_1');

        $item = $list->createItemFromArray(array(
            'key_1' => 'value 1',
            'key_2' => 'value 2'
        ));
        $this->assertInstanceOf(INabuDataReadable::class, $item);
        $this->assertSame(1, count($list));
        $this->assertSame('value 1', $item->getValue('key_1'));
        $this->assertSame('value 2', $item->getValue('key_2'));
    }

    /**
     * @test merge
     */
    public function testMerge()
    {
        $list_left = new CNabuAbstractDataListTesting('key_field');
        for ($i = 1; $i < 21; $i = $i + 2) {
            $list_left->addItem(new CNabuAbstractDataListObjectTesting(
                array(
                    'key_field' => $i,
                    'key_value' => "value $i"
                )
            ));
        }

        $list_right = new CNabuAbstractDataListTesting('key_field');
        for ($i = 2; $i < 22; $i = $i + 2) {
            $list_right->addItem(new CNabuAbstractDataListObjectTesting(
                array(
                    'key_field' => $i,
                    'key_value' => "value $i"
                )
            ));
        }

        $merge_list = new CNabuAbstractDataListTesting('key_field');
        $merge_list->merge($list_left);
        $merge_list->merge($list_right);

        $this->assertSame(20, count($merge_list));
    }

    /**
     * @test mergeArray
     */
    public function testMergeArray()
    {
        $list_left = array();
        for ($i = 1; $i < 21; $i = $i + 2) {
            $list_left[$i] = new CNabuAbstractDataListObjectTesting(
                array(
                    'key_field' => $i,
                    'key_value' => "value $i"
                )
            );
        }

        $list_right = array();
        for ($i = 2; $i < 22; $i = $i + 2) {
            $list_right[$i] = new CNabuAbstractDataListObjectTesting(
                array(
                    'key_field' => $i,
                    'key_value' => "value $i"
                )
            );
        }

        $merge_list = new CNabuAbstractDataListTesting('key_field');
        $merge_list->mergeArray($list_left);
        $merge_list->mergeArray($list_right);

        $this->assertSame(20, count($merge_list));
    }

    /**
     * @test __construct
     * @test count
     * @test current
     * @test next
     * @test key
     * @test valid
     * @test rewind
     */
    public function testConstructFromDataList()
    {
        $arrobj = array();
        $arrobj2 = array();

        for ($i = 1; $i < 11; $i++) {
            $arrobj[$i - 1] = array(
                'key_field' => $i,
                'key_value' => "value $i"
            );
            $arrobj2[$i + 9] = array(
                'key_field' => $i + 10,
                'key_value' => 'value ' . ($i + 10)
            );
        }
        $this->assertSame(10, count($arrobj));
        $this->assertSame(10, count($arrobj2));

        $arrmerge = array_merge($arrobj, $arrobj2);
        $this->assertSame(20, count($arrmerge));

        $list = new CNabuAbstractDataListTesting('key_field', $arrobj);
        $this->assertSame(10, count($list));

        $copy = new CNabuAbstractDataListTesting('key_field', $arrobj2);
        $this->assertSame(10, count($copy));

        $merge = new CNabuAbstractDataListTesting('key_field', $list);
        $merge->merge($copy);
        $this->assertSame(20, count($merge));

        $i = 0;
        foreach ($merge as $key => $value) {
            $this->assertInstanceOf(CNabuAbstractDataListObjectTesting::class, $value);
            $this->assertSame($arrmerge[$i]['key_field'], $value->getValue('key_field'));
            $this->assertSame($arrmerge[$i]['key_value'], $value->getValue('key_value'));
            $i++;
        }
        $this->assertSame(20, $i);
    }

    /**
     * @test __construct
     * @test count
     * @test current
     * @test next
     * @test key
     * @test valid
     * @test rewind
     */
    public function testConstructFromArray()
    {
        $arrobj = array();
        $arrobj2 = array();

        for ($i = 1; $i < 11; $i++) {
            $arrobj[$i - 1] = array(
                'key_field' => $i,
                'key_value' => "value $i"
            );
            $arrobj2[$i + 9] = array(
                'key_field' => $i + 10,
                'key_value' => 'value ' . ($i + 10)
            );
        }
        $this->assertSame(10, count($arrobj));
        $this->assertSame(10, count($arrobj2));

        $arrmerge = array_merge($arrobj, $arrobj2);
        $this->assertSame(20, count($arrmerge));

        $list = new CNabuAbstractDataListTesting(null, $arrobj);
        $this->assertSame(10, count($list));

        $copy = new CNabuAbstractDataListTesting(null, $arrobj2);
        $this->assertSame(10, count($copy));

        $merge = new CNabuAbstractDataListTesting(null, $list);
        $merge->merge($copy);
        $this->assertSame(20, count($merge));

        $i = 0;
        foreach ($merge as $key => $value) {
            $this->assertInstanceOf(CNabuAbstractDataListObjectTesting::class, $value);
            $this->assertSame($arrmerge[$i]['key_field'], $value->getValue('key_field'));
            $this->assertSame($arrmerge[$i]['key_value'], $value->getValue('key_value'));
            $i++;
        }
        $this->assertSame(20, $i);
    }

    /**
     * @test __construct
     */
    public function testConstructFails()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(TRIGGER_ERROR_INVALID_ARGUMENT, '$source_list'));
        $list = new CNabuAbstractDataListTesting(null, 23);
    }

    /**
     * @test addItem
     */
    public function testAddItemFails()
    {
        $list = new CNabuAbstractDataListTesting();

        $this->expectException(Error::class);
        $this->expectExceptionMessage(sprintf(TRIGGER_ERROR_INVALID_ARGUMENT, '$key'));
        $list->addItem(new CNabuAbstractDataListObjectTesting(), null);
    }

    /**
     * @test mergeArray
     */
    public function testMergeArrayFails()
    {
        $list = new CNabuAbstractDataListTesting();

        $this->expectException(UnexpectedValueException::class);
        $list->mergeArray(array(58));
    }

    /**
     * @test locateKey
     */
    public function testLocateKeyFails()
    {
        $this->expectException(UnexpectedValueException::class);
        $list = new CNabuAbstractDataListTesting('key_exception', array('key_exception' => 1, 'key_value' => 'val 1'));
    }
}

class CNabuAbstractDataListTesting extends CNabuAbstractDataList
{
    protected function acquireItem($key): ?INabuDataReadable
    {
        return null;
    }

    protected function createDataInstance(array $data): ?\nabu\data\interfaces\INabuDataReadable
    {
        return new CNabuAbstractDataListObjectTesting($data);
    }
}

class CNabuAbstractDataListObjectTesting extends CNabuAbstractDataObject
{

}
