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

use stdClass;

use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\Error\Notice;

use PHPUnit\Framework\TestCase;

/**
 * PHPUnit tests to verify functionality of class @see { CNabuRODataObject }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package nabu\data
 */
class CNabuDataObjectTest extends TestCase
{
    /**
     * @test __construct
     * @test isEditable
     * @test isReadOnly
     * @test setAsEditable
     * @test setAsReadOnly
     */
    public function testConstruct()
    {
        $object = new CNabuDataObjectTestingWR();
        $this->assertTrue($object->isEditable());
        $this->assertFalse($object->isReadOnly());

        $this->assertSame($object, $object->setAsReadOnly());
        $this->assertTrue($object->isReadOnly());
        $this->assertFalse($object->isEditable());

        $this->assertSame($object, $object->setAsEditable());
        $this->assertTrue($object->isEditable());
        $this->assertFalse($object->isReadOnly());
    }

    /**
     * @test reset
     */
    public function testReset()
    {
        $object = new CNabuDataObjectTestingWR(
            array(
                'test_name' => 'test_value'
            )
        );
        $this->assertFalse($object->isEmpty());
        $object->reset();
        $this->assertTrue($object->isEmpty());

        $object = new CNabuDataObjectTestingWR();
        $this->assertTrue($object->isEmpty());
        $this->assertFalse($object->reset());

        $object = new CNabuDataObjectTestingWR();
        $object->setAsReadOnly();
        $this->expectException(Error::class);
        $object->reset();
    }

    /**
     * @test setValue
     * @test setAsEditable
     * @test setAsReadOnly
     * @test removeValue
     * @test renameValue
     */
    public function testSetValueScalar()
    {
        $object = new CNabuDataObjectTestingWR();
        $this->assertTrue($object->isEditable());
        $this->assertSame($object, $object->setValue('test_name', 'test_value'));
        $this->assertSame('test_value', $object->getValue('test_name'));
        $this->assertSame($object, $object->setValue('other_name', 10));
        $this->assertSame(10, $object->getValue('other_name'));
        $this->assertSame($object, $object->removeValue('test_name'));
        $this->assertFalse($object->hasValue('test_name'));
        $this->assertSame($object, $object->renameValue('other_name', 'new_name'));
        $this->assertFalse($object->hasValue('other_name'));
        $this->assertTrue($object->hasValue('new_name'));
        $this->assertSame(10, $object->getValue('new_name'));

        $this->assertSame($object, $object->setAsReadOnly());
        $this->expectException(Error::class);
        $object->setValue('test_name', 'test_value');
    }

    /**
     * @test setValue
     */
    public function testValueArray()
    {
        $object = new CNabuDataObjectTestingWR();
        $this->assertTrue($object->isEditable());
        $this->expectException(Notice::class);
        $object->setValue('test_array', array(1, 2, 3, 4));
    }

    /**
     * @test setValue
     */
    public function testValueObject()
    {
        $object = new CNabuDataObjectTestingWR();
        $this->assertTrue($object->isEditable());
        $this->expectException(Notice::class);
        $object->setValue('test_object', new stdClass());
    }

    /**
     * @test setArrayValues
     */
    public function testSetArrayValues()
    {
        $object = new CNabuDataObjectTestingWR();
        $this->assertTrue($object->isEditable());
        $this->assertSame($object, $object->setArrayValues(array('a' => 1, 'b' => 2)));
        $this->assertSame(1, $object->getValue('a'));
        $this->assertSame(2, $object->getValue('b'));
        $this->assertSame($object, $object->setArrayValues(array('a' => 3, 'c' => 5)));
        $this->assertSame(3, $object->getValue('a'));
        $this->assertSame(2, $object->getValue('b'));
        $this->assertSame(5, $object->getValue('c'));

        $object->setAsReadOnly();
        $this->expectException(Error::class);
        $object->setArrayValues(array('a' => 10, 'd' => 6));
    }

    /**
     * @test transferValue
     */
    public function testTransferValue()
    {
        $obj_source = new CNabuDataObjectTestingWR();
        $this->assertTrue($obj_source->isEditable());
        $obj_source->setValue('test_name', 20);

        $obj_target = new CNabuDataObjectTestingWR();
        $this->assertTrue($obj_target->isEditable());

        $obj_target->transferValue($obj_source, 'test_name');
        $this->assertTrue($obj_target->hasValue('test_name'));
        $this->assertSame(20, $obj_target->getValue('test_name'));

        $obj_source->setValue('test_name', 40);
        $obj_target->transferValue($obj_source, 'test_name');
        $this->assertSame(40, $obj_target->getValue('test_name'));

        $obj_source->setValue('other_test', 'white');
        $obj_target->transferValue($obj_source, 'other_test', 'test_name');
        $this->assertSame('white', $obj_target->getValue('test_name'));

        $obj_target->setAsReadOnly();
        $this->expectException(Error::class);
        $obj_target->transferValue($obj_source, 'test_name');
    }

    /**
     * @test testTransferMixedValue
     */
    public function testTransferMixedValue()
    {
        $obj_source = new CNabuDataObjectTestingWR();
        $this->assertTrue($obj_source->isEditable());
        $obj_source->setValue('test_name', 20);

        $obj_target = new CNabuDataObjectTestingWR();
        $this->assertTrue($obj_target->isEditable());

        $obj_target->transferMixedValue($obj_source, 'test_name');
        $this->assertTrue($obj_target->hasValue('test_name'));
        $this->assertSame(20, $obj_target->getValue('test_name'));

        $obj_source->setValue('test_name', 40);
        $obj_target->transferMixedValue($obj_source, 'test_name');
        $this->assertSame(40, $obj_target->getValue('test_name'));

        $obj_source->setValue('other_test', 'white');
        $obj_target->transferMixedValue($obj_source, 'other_test', 'test_name');
        $this->assertSame('white', $obj_target->getValue('test_name'));

        $obj_target->transferMixedValue(90, 'test_name');
        $this->assertSame(90, $obj_target->getValue('test_name'));

        $obj_target->transferMixedValue(100, 'other_test', 'test_name');
        $this->assertSame(100, $obj_target->getValue('test_name'));

        $obj_target->setAsReadOnly();
        $this->expectException(Error::class);
        $obj_target->transferMixedValue($obj_source, 'test_name');
    }

    /**
     * @test exchangeValue
     */
    public function testExchangeValue()
    {
        $obj_source = new CNabuDataObjectTestingWR();
        $this->assertTrue($obj_source->isEditable());
        $obj_source->setValue('test_name', 20);

        $obj_target = new CNabuDataObjectTestingWR();
        $this->assertTrue($obj_target->isEditable());
        $obj_target->setValue('test_name', 30);

        $obj_target->exchangeValue($obj_source, 'test_name');
        $this->assertSame(30, $obj_source->getValue('test_name'));
        $this->assertSame(20, $obj_target->getValue('test_name'));

        $obj_source->setValue('other_test', 40);
        $obj_target->exchangeValue($obj_source, 'other_test', 'test_name');
        $this->assertSame(30, $obj_source->getValue('test_name'));
        $this->assertSame(20, $obj_source->getValue('other_test'));
        $this->assertSame(40, $obj_target->getValue('test_name'));
        $this->assertFalse($obj_target->hasValue('other_test'));

        $obj_target->setAsReadOnly();
        $this->expectException(Error::class);
        $obj_target->exchangeValue($obj_source, 'test_name');
    }

    /**
     * @test copyData
     */
    public function testCopyData()
    {
        $obj_source = new CNabuDataObjectTestingWR();
        $this->assertTrue($obj_source->isEditable());
        $obj_source->setValue('test_name', 20);
        $obj_source->setValue('other_test', 'string');

        $obj_target = new CNabuDataObjectTestingWR();
        $this->assertTrue($obj_target->isEditable());
        $obj_target->copyData($obj_source);
        $this->assertSame(20, $obj_source->getValue('test_name'));
        $this->assertSame('string', $obj_source->getValue('other_test'));
        $this->assertFalse($obj_target->isEmpty());
        $this->assertSame(20, $obj_target->getValue('test_name'));
        $this->assertSame('string', $obj_target->getValue('other_test'));

        $obj_target->setAsReadOnly();
        $this->expectException(Error::class);
        $obj_target->copyData($obj_source);
    }
}

class CNabuDataObjectTestingWR extends CNabuDataObject
{

}
