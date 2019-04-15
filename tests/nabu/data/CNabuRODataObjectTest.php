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
     * @test isValueEqualThan
     */
    public function testConstruct()
    {
        $object = new CNabuDataObjectTestingRO();
        $this->assertTrue($object->isEmpty());
        $this->assertFalse($object->hasValue('some_name'));

        $object = new CNabuDataObjectTestingRO(
            array(
                'value_int' => 2,
                'value_float' => 3.456,
                'value_string' => 'test_field',
                'value_string_int' => '2',
                'value_null' => null,
                'value_array' => array('item1', 'item2', 'item3'),
                'value_list' => '3, 4, 10, 85',
                'value_empty_0' => 0,
                'value_empty_false' => false,
                'value_empty_string' => ''
            )
        );
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

        $this->assertTrue($object->hasValue('value_string'));
        $this->assertFalse($object->isValueNumeric('value_string'));
        $this->assertFalse($object->isValueFloat('value_string'));
        $this->assertTrue($object->isValueString('value_string'));
        $this->assertFalse($object->isValueEmptyString('value_string'));
        $this->assertFalse($object->isValueNull('value_string'));
        $this->assertFalse($object->isValueGUID('value_string'));
        $this->assertTrue($object->isValueEqualTo('value_string', 'test_field'));
        $this->assertFalse($object->isValueEqualTo('value_string', 'other value', true));

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
    }

}

class CNabuDataObjectTestingRO extends CNabuDataObject
{

}
