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

use PHPUnit\Framework\TestCase;

class CNabuRODataObjectTest extends TestCase
{
    /**
     * @test __construct
     * @test isEmpty
     * @test hasValue
     * @test isValueNumeric
     * @test isValueFloat
     * @test isValueString
     * @test isValueEmptyString
     * @test isValueNull
     * @test isValueEmpty
     * @test isValueGUID
     * @test isValueEqualTo
     * @test getValue
     * @test getValueAsList
     * @test matchValue
     */
    public function testConstruct()
    {
        $object = new CNabuDataObjectTestingRO();
        $this->assertTrue($object->isEmpty());
        $this->assertFalse($object->hasValue('some_name'));

        $data = array(
            'value_int' => 2,
            'value_float' => 3.456,
            'value_string' => 'test_field',
            'value_string_int' => '2',
            'value_string_float' => '3.456',
            'value_guid' => nb_generateGUID(),
            'value_null' => null,
            'value_array' => array('item1', 'item2', 'item3'),
            'value_list' => '3,4  , 10,   85',
            'value_empty_0' => 0,
            'value_empty_false' => false,
            'value_empty_string' => ''
        );

        $object = new CNabuDataObjectTestingRO($data);

        $this->assertFalse($object->isEmpty());
        $this->assertFalse($object->hasValue('some_name'));

        $this->assertTrue($object->hasValue('value_int'));
        $this->assertTrue($object->isValueNumeric('value_int'));
        $this->assertTrue($object->isValueFloat('value_int'));
        $this->assertFalse($object->isValueString('value_int'));
        $this->assertFalse($object->isValueEmptyString('value_int'));
        $this->assertFalse($object->isValueNull('value_int'));
        $this->assertFalse($object->isValueEmpty('value_int'));
        $this->assertFalse($object->isValueGUID('value_int'));
        $this->assertTrue($object->isValueEqualTo('value_int', 2));
        $this->assertTrue($object->isValueEqualTo('value_int', '2'));
        $this->assertFalse($object->isValueEqualTo('value_int', '2', true));
        $this->assertFalse($object->isValueEqualTo('value_int', 3));
        $this->assertIsInt($object->getValue('value_int'));
        $this->assertSame(2, $object->getValue('value_int'));

        $this->assertTrue($object->hasValue('value_float'));
        $this->assertTrue($object->isValueNumeric('value_float'));
        $this->assertTrue($object->isValueFloat('value_float'));
        $this->assertFalse($object->isValueString('value_float'));
        $this->assertFalse($object->isValueEmptyString('value_float'));
        $this->assertFalse($object->isValueNull('value_float'));
        $this->assertFalse($object->isValueGUID('value_float'));
        $this->assertTrue($object->isValueEqualTo('value_float', 3.456));
        $this->assertTrue($object->isValueEqualTo('value_float', '3.456'));
        $this->assertFalse($object->isValueEqualTo('value_float', '3.456', true));
        $this->assertFalse($object->isValueEqualTo('value_float', 3));
        $this->assertIsFloat($object->getValue('value_float'));
        $this->assertSame(3.456, $object->getValue('value_float'));

        $this->assertTrue($object->hasValue('value_string'));
        $this->assertFalse($object->isValueNumeric('value_string'));
        $this->assertFalse($object->isValueFloat('value_string'));
        $this->assertTrue($object->isValueString('value_string'));
        $this->assertFalse($object->isValueEmptyString('value_string'));
        $this->assertFalse($object->isValueNull('value_string'));
        $this->assertFalse($object->isValueGUID('value_string'));
        $this->assertTrue($object->isValueEqualTo('value_string', 'test_field'));
        $this->assertFalse($object->isValueEqualTo('value_string', 'other value', true));
        $this->assertIsString($object->getValue('value_string'));
        $this->assertSame('test_field', $object->getValue('value_string'));

        $this->assertTrue($object->hasValue('value_string_int'));
        $this->assertTrue($object->isValueNumeric('value_string_int'));
        $this->assertTrue($object->isValueFloat('value_string_int'));
        $this->assertTrue($object->isValueString('value_string_int'));
        $this->assertFalse($object->isValueEmptyString('value_string_int'));
        $this->assertFalse($object->isValueNull('value_string_int'));
        $this->assertFalse($object->isValueGUID('value_string_int'));
        $this->assertTrue($object->isValueEqualTo('value_string_int', '2'));
        $this->assertTrue($object->isValueEqualTo('value_string_int', 2));
        $this->assertFalse($object->isValueEqualTo('value_string_int', 2, true));
        $this->assertFalse($object->isValueEqualTo('value_string_int', 'other value'));
        $this->assertIsString($object->getValue('value_string_int'));
        $this->assertSame('2', $object->getValue('value_string_int'));

        $this->assertTrue($object->hasValue('value_string_float'));
        $this->assertTrue($object->isValueNumeric('value_string_float'));
        $this->assertTrue($object->isValueFloat('value_string_float'));
        $this->assertTrue($object->isValueString('value_string_float'));
        $this->assertFalse($object->isValueEmptyString('value_string_float'));
        $this->assertFalse($object->isValueNull('value_string_float'));
        $this->assertFalse($object->isValueGUID('value_string_float'));
        $this->assertTrue($object->isValueEqualTo('value_string_float', '3.456'));
        $this->assertTrue($object->isValueEqualTo('value_string_float', 3.456));
        $this->assertFalse($object->isValueEqualTo('value_string_float', 3.456, true));
        $this->assertFalse($object->isValueEqualTo('value_string_float', 'other value'));
        $this->assertIsString($object->getValue('value_string_float'));
        $this->assertSame('3.456', $object->getValue('value_string_float'));

        $this->assertTrue($object->hasValue('value_guid'));
        $this->assertFalse($object->isValueNumeric('value_guid'));
        $this->assertFalse($object->isvalueFloat('value_guid'));
        $this->assertTrue($object->isValueString('value_guid'));
        $this->assertFalse($object->isValueEmptyString('value_guid'));
        $this->assertFalse($object->isValueNull('value_guid'));
        $this->assertTrue($object->isValueGUID('value_guid'));
        $this->assertTrue($object->isValueEqualTo('value_guid', $data['value_guid']));
        $this->assertIsString($object->getValue('value_guid'));
        $this->assertSame($data['value_guid'], $object->getValue('value_guid'));

        $this->assertTrue($object->hasValue('value_null'));
        $this->assertFalse($object->isValueNumeric('value_null'));
        $this->assertFalse($object->isValueFloat('value_null'));
        $this->assertFalse($object->isValueString('value_null'));
        $this->assertFalse($object->isValueEmptyString('value_null'));
        $this->assertTrue($object->isValueNull('value_null'));
        $this->assertFalse($object->isValueGUID('value_null'));
        $this->assertTrue($object->isValueEqualTo('value_null', null));
        $this->assertFalse($object->isValueEqualTo('value_null', 'null'));
        $this->assertNull($object->getValue('value_null'));

        $this->assertTrue($object->hasValue('value_array'));
        $this->assertFalse($object->isValueNumeric('value_array'));
        $this->assertFalse($object->isValueFloat('value_array'));
        $this->assertFalse($object->isValueString('value_array'));
        $this->assertFalse($object->isValueEmptyString('value_array'));
        $this->assertFalse($object->isValueNull('value_array'));
        $this->assertFalse($object->isValueGUID('value_array'));
        $this->assertTrue($object->isValueEqualTo('value_array', array('item1', 'item2', 'item3')));
        $this->assertFalse($object->isValueEqualTo('value_array', array('item3', 'item2', 'item1')));
        $this->assertFalse($object->isValueEqualTo('value_array', array('item5', 'item6')));
        $this->assertFalse($object->isValueEqualTo('value_array', null));
        $this->assertIsArray($object->getValue('value_array'));
        $this->assertSame(array('item1', 'item2', 'item3'), $object->getValue('value_array'));

        $this->assertTrue($object->hasValue('value_list'));
        $this->assertFalse($object->isValueNumeric('value_list'));
        $this->assertFalse($object->isValueFloat('value_list'));
        $this->assertTrue($object->isValueString('value_list'));
        $this->assertFalse($object->isValueEmptyString('value_list'));
        $this->assertFalse($object->isValueNull('value_list'));
        $this->assertFalse($object->isValueGUID('value_list'));
        $this->assertIsArray($object->getValueAsList('value_list'));
        $this->assertSame(array('3', '4', '10', '85'), $object->getValueAsList('value_list'));
        $this->assertNotSame(array(3, 4, 10, 85), $object->getValueAsList('value_list'));

        $this->assertTrue($object->hasValue('value_empty_0'));
        $this->assertTrue($object->isValueEmpty('value_empty_0'));

        $this->assertTrue($object->hasValue('value_empty_false'));
        $this->assertTrue($object->isValueEmpty('value_empty_false'));

        $this->assertTrue($object->hasValue('value_empty_string'));
        $this->assertTrue($object->isValueEmpty('value_empty_string'));

        $this->assertTrue($object->matchValue($object, 'value_int'));
        $this->assertTrue($object->matchValue($object, 'value_int', null, true));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_float'));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_float', true));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_string'));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_string', true));
        $this->assertTrue($object->matchValue($object, 'value_int', 'value_string_int'));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_string_int', true));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_string_int', true));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_string_float'));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_string_float', true));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_null'));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_null', true));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_array'));
        $this->assertFalse($object->matchValue($object, 'value_int', 'value_array', true));
    }

    /**
     * @test dump
     */
    public function testDump()
    {
        $object = new CNabuDataObjectTestingRO(
            array(
                'test_name' => 'test_value'
            )
        );

        $this->assertSame("array (\n  'test_name' => 'test_value',\n)", $object->dump());
    }

    /**
     * @test reset
     * @test isEmpty
     */
    public function testReset()
    {
        $object = new CNabuDataObjectTestingRO(
            array(
                'test_name' => 'test_value'
            )
        );
        $this->assertFalse($object->isEmpty());
        $object->reset();
        $this->assertTrue($object->isEmpty());

        $object = new CNabuDataObjectTestingRO();
        $this->assertTrue($object->isEmpty());
    }
}

class CNabuDataObjectTestingRO extends CNabuDataObject
{

}
