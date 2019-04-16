<?php

/** @license
 *  Copyright 2019-2011 Rafael Gutierrez Martinez
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
 * @since 3.0.0 Surface
 * @version 3.0.0 Surface
 * @package tests\nabu\min
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
     * @test setValue
     * @test setAsEditable
     * @test setAsReadOnly
     */
    public function testSetValueScalar()
    {
        $object = new CNabuDataObjectTestingWR();
        $this->assertTrue($object->isEditable());
        $this->assertSame($object, $object->setValue('test_name', 'test_value'));
        $this->assertSame('test_value', $object->getValue('test_name'));
        $this->assertSame($object, $object->setValue('other_name', 10));
        $this->assertSame(10, $object->getValue('other_name'));

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
}

class CNabuDataObjectTestingWR extends CNabuDataObject
{

}
