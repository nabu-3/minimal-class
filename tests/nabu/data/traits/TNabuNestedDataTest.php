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

use stdClass;

use PHPUnit\Framework\Error\Error;

use PHPUnit\Framework\TestCase;

use nabu\data\CNabuDataObject;
use nabu\data\CNabuRODataObject;

/**
 * PHPUnit tests to verify functionality of class @see { TNabuJSONData }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package nabu\data\traits
 */
class TNabuNestedDataTest extends TestCase
{
    /**
     * @test getValueAsJSONDecoded
     * @test setValueAsJSONEncoded
     */
    public function testGetValueAsJSONDecoded()
    {
        $array = array('a' => 1, 'b' => '2');
        $object = new CNabuNestedDataTestingRO(
            array(
                'json_name' => json_encode(array('a' => 1, 'b' => '2'), JSON_OBJECT_AS_ARRAY)
            )
        );
        $this->assertSame($array, $object->getValueAsJSONDecoded('json_name'));
        $this->expectException(Error::class);
        $object->setValueAsJSONEncoded('json_name', '{}');
    }

    /**
     * @test getValueAsJSONDecoded
     * @test setValueAsJSONEncoded
     */
    public function testSetValueAsJSONDecoded()
    {
        $array = array('a' => 1, 'b' => '2');
        $arrtxt = json_encode($array, JSON_OBJECT_AS_ARRAY);
        $object = new CNabuNestedDataTestingWR();

        $object->setValueAsJSONEncoded('json_name', null);
        $this->assertNull($object->getValueAsJSONDecoded('json_name'));

        $object->setValueAsJSONEncoded('json_name', array());
        $this->assertSame(array(), $object->getValueAsJSONDecoded('json_name'));

        $object->setValueAsJSONEncoded('json_name', 'no_json_string');
        $this->assertSame(array('no_json_string'), $object->getValueAsJSONDecoded('json_name'));

        $object->setValueAsJSONEncoded('json_name', $arrtxt);
        $this->assertSame($array, $object->getValueAsJSONDecoded('json_name'));
        $this->assertSame($arrtxt, $object->getValue('json_name'));

        $object->setValueAsJSONEncoded('json_name', $array);
        $this->assertSame($array, $object->getValueAsJSONDecoded('json_name'));
        $this->assertSame($arrtxt, $object->getValue('json_name'));

        $arrobj = new stdClass();
        $object->setValueAsJSONEncoded('json_name', $arrobj);
        $this->assertSame(array(), $object->getValueAsJSONDecoded('json_name'));

        $arrobj->a = 1;
        $arrobj->b = '2';
        $object->setValueAsJSONEncoded('json_name', $arrobj);
        $this->assertSame($array, $object->getValueAsJSONDecoded('json_name'));
        $this->assertSame($arrtxt, $object->getValue('json_name'));

        $object->setAsReadOnly();
        $this->expectException(Error::class);
        $object->setValueAsJSONEncoded('json_name', '{}');
    }

    /**
     * @test getValue
     * @test checkPath
     */
    public function testGetValueRO()
    {
        $array = array(
            'a' => 1,
            'b' => array(
                'c' => 2
            )
        );

        $object = new CNabuNestedDataTestingRO($array);
        $this->assertSame(1, $object->getValue('a'));
        $this->assertSame(array('c' => 2), $object->getValue('b'));
        $this->assertSame(2, $object->getValue('b.c'));
        $this->assertNull($object->getValue('a.b.c'));
        $this->assertTrue($object->checkPath('a'));
        $this->assertTrue($object->checkPath('b'));
        $this->assertTrue($object->checkPath('b.c'));
        $this->assertFalse($object->checkPath('b.2'));
        $this->assertFalse($object->checkPath('a.b.c'));
        $this->assertFalse($object->checkPath('b.c.d'));
        $this->assertFalse($object->checkPath('b.c.2'));
    }

    /**
     * @test setValue
     * @test checkPath
     */
    public function testSetValueRO()
    {
        $object = new CNabuNestedDataTestingRO();
        $this->expectException(Error::class);
        $object->setValue('a', 1);
    }

    /**
     * @test SetValue
     * @test checkPath
     * @test getValue
     */
    public function testSetValueWR()
    {
        $object = new CNabuNestedDataTestingWR();
    }
}

class CNabuNestedDataTestingRO extends CNabuRODataObject
{
    use TNabuNestedData;
}

class CNabuNestedDataTestingWR extends CNabuDataObject
{
    use TNabuNestedData;
}
