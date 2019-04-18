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
     * @test hasValue
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
        $this->assertTrue($object->hasValue('a'));
        $this->assertTrue($object->hasValue('b'));
        $this->assertTrue($object->hasValue('b.c'));
        $this->assertFalse($object->hasValue('b.2'));
        $this->assertFalse($object->hasValue('a.b.c'));
        $this->assertFalse($object->hasValue('b.c.d'));
        $this->assertFalse($object->hasValue('b.c.2'));
    }

    /**
     * @test hasValue
     * @test grantPath
     */
    public function testGrantPathRO()
    {
        $object = new CNabuNestedDataTestingRO();

        $this->expectException(Error::class);
        $object->grantPath('a');
    }

    /**
     * @test hasValue
     * @test grantPath
     * @test hasValue
     */
    public function testGrantPathWR()
    {
        $object = new CNabuNestedDataTestingWR();

        $this->assertTrue($object->grantPath('a'));
        $this->assertTrue($object->hasValue('a'));
        $this->assertTrue($object->isValueNull('a'));

        $this->assertTrue($object->grantPath('b'));
        $this->assertTrue($object->hasValue('a'));
        $this->assertTrue($object->hasValue('b'));
        $this->assertTrue($object->isValueNull('a'));
        $this->assertTrue($object->isValueNull('b'));

        $this->assertTrue($object->grantPath('c.d'));
        $this->assertTrue($object->hasValue('c.d'));
        $this->assertSame(array('d' => null), $object->getValue('c'));
        $this->assertNull($object->getValue('c.d'));
        $this->assertTrue($object->hasValue('a'));
        $this->assertTrue($object->hasValue('b'));
        $this->assertTrue($object->isValueNull('a'));
        $this->assertTrue($object->isValueNull('b'));
        $this->assertFalse($object->isValueNull('c'));
        $this->assertTrue($object->isValueNull('c.d'));

        $this->assertTrue($object->grantPath('c.d.e'));
        $this->assertTrue($object->hasValue('c.d'));
        $this->assertTrue($object->hasValue('c.d.e'));
        $this->assertSame(array('d' => array('e' => null)), $object->getValue('c'));
        $this->assertSame(array('e' => null), $object->getValue('c.d'));
        $this->assertNull($object->getValue('c.d.e'));
        $this->assertTrue($object->isValueNull('c.d.e'));
        $this->assertTrue($object->hasValue('a'));
        $this->assertTrue($object->hasValue('b'));
        $this->assertTrue($object->isValueNull('a'));
        $this->assertTrue($object->isValueNull('b'));
        $this->assertFalse($object->isValueNull('c'));
        $this->assertFalse($object->isValueNull('c.d'));

        $this->assertTrue($object->grantPath('b.x.z'));
        $this->assertTrue($object->hasValue('b.x'));
        $this->assertTrue($object->hasValue('b.x.z'));
        $this->assertSame(array('x' => array('z' => null)), $object->getValue('b'));
        $this->assertSame(array('z' => null), $object->getValue('b.x'));
        $this->assertNull($object->getValue('b.x.z'));
        $this->assertTrue($object->isValueNull('b.x.z'));
        $this->assertTrue($object->hasValue('a'));
        $this->assertTrue($object->hasValue('b'));
        $this->assertTrue($object->isValueNull('a'));
        $this->assertFalse($object->isValueNull('b'));
        $this->assertFalse($object->isValueNull('c'));
        $this->assertFalse($object->isValueNull('c.d'));
        $this->assertSame(array('d' => array('e' => null)), $object->getValue('c'));
        $this->assertSame(array('e' => null), $object->getValue('c.d'));
        $this->assertTrue($object->isValueNull('c.d.e'));
    }

    /**
     * @test setValue
     * @test hasValue
     */
    public function testSetValueRO()
    {
        $object = new CNabuNestedDataTestingRO();
        $this->expectException(Error::class);
        $object->setValue('a', 1);
    }

    /**
     * @test setValue
     * @test hasValue
     * @test getValue
     * @test CNabuRODataObject::isValueEqualTo
     */
    public function testSetValueWR()
    {
        $object = new CNabuNestedDataTestingWR();
        $this->assertTrue($object->isEditable());
        $object->setValue('a', 1);
        $this->assertTrue($object->isValueEqualTo('a', 1, true));
        $object->setValue('b', '2');
        $this->assertTrue($object->isValueEqualTo('b', 2));
        $this->assertTrue($object->isValueEqualTo('b', '2', true));
        $this->assertFalse($object->isValueEqualTo('b', 2, true));
        $object->setValue('a.c', 5);
        $this->assertTrue($object->isValueEqualTo('a.c', 5));
        $this->assertTrue($object->isValueEqualTo('a.c', 5, true));
        $this->assertTrue($object->isValueEqualTo('a.c', '5'));
        $this->assertFalse($object->isValueEqualTo('a.c', '5', true));
        $object->setValue('b.d', 10);
        $this->assertTrue($object->hasValue('b.d'));
        $this->assertTrue($object->isValueEqualTo('b.d', 10));
        $object->setValue('b.d', 20);
        $this->assertTrue($object->hasValue('b.d'));
        $this->assertTrue($object->isValueEqualTo('b.d', 20));
        $object->setValue('b.d', 30, 0);
        $this->assertTrue($object->hasValue('b.d'));
        $this->assertTrue($object->isValueEqualTo('b.d', 30));
        $object->setValue('e', 50, 0);
        $this->assertTrue($object->isValueEqualTo('e', 50));
        $object->setValue('e.f', 60, 0);
        $this->assertFalse($object->hasValue('e.f'));
        $this->assertTrue($object->isValueEqualTo('e', 50));
    }

    /**
     * @test CNabuRODataObject::isValueNull
     * @test CNabuRODataObject::isValueEmpty
     * @test CNabuRODataObject::isValueNumeric
     * @test CNabuRODataObject::isValueFloat
     * @test CNabuRODataObject::isValueString
     * @test CNabuRODataObject::isValueEmptyString
     * @test CNabuRODataObject::isValueGUID
     */
    public function testInheritedIsValueXMethods()
    {
        $object = new CNabuNestedDataTestingWR();
        $this->assertTrue($object->isEditable());

        $object->setValue('test.null', null);
        $this->assertTrue($object->isValueNull('test.null'));
        $this->assertTrue($object->isValueEmpty('test.null'));
        $this->assertFalse($object->isValueNumeric('test.null'));
        $this->assertFalse($object->isValueFloat('test.null'));
        $this->assertFalse($object->isValueString('test.null'));
        $this->assertFalse($object->isValueEmptyString('test.null'));
        $this->assertFalse($object->isValueGUID('test.null'));

        $object->setValue('test.numeric', 0);
        $this->assertFalse($object->isValueNull('test.numeric'));
        $this->assertTrue($object->isValueEmpty('test.numeric'));
        $this->assertTrue($object->isValueNumeric('test.numeric'));
        $this->assertTrue($object->isValueFloat('test.numeric'));
        $this->assertFalse($object->isValueString('test.numeric'));
        $this->assertFalse($object->isValueEmptyString('test.numeric'));
        $this->assertFalse($object->isValueGUID('test.numeric'));

        $object->setValue('test.numeric', 10);
        $this->assertFalse($object->isValueNull('test.numeric'));
        $this->assertFalse($object->isValueEmpty('test.numeric'));
        $this->assertTrue($object->isValueNumeric('test.numeric'));
        $this->assertTrue($object->isValueFloat('test.numeric'));
        $this->assertFalse($object->isValueString('test.numeric'));
        $this->assertFalse($object->isValueEmptyString('test.numeric'));
        $this->assertFalse($object->isValueGUID('test.numeric'));

        $object->setValue('test.float', 10.5);
        $this->assertFalse($object->isValueNull('test.float'));
        $this->assertFalse($object->isValueEmpty('test.float'));
        $this->assertTrue($object->isValueNumeric('test.float'));
        $this->assertTrue($object->isValueFloat('test.float'));
        $this->assertFalse($object->isValueString('test.float'));
        $this->assertFalse($object->isValueEmptyString('test.float'));
        $this->assertFalse($object->isValueGUID('test.float'));

        $object->setValue('test.string', '');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertTrue($object->isValueEmpty('test.string'));
        $this->assertFalse($object->isValueNumeric('test.string'));
        $this->assertFalse($object->isValueFloat('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertTrue($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));

        $object->setValue('test.string', 'test value');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertfalse($object->isValueEmpty('test.string'));
        $this->assertFalse($object->isValueNumeric('test.string'));
        $this->assertFalse($object->isValueFloat('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));

        $object->setValue('test.string', 'null');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertfalse($object->isValueEmpty('test.string'));
        $this->assertFalse($object->isValueNumeric('test.string'));
        $this->assertFalse($object->isValueFloat('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));

        $object->setValue('test.string', 'false');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertfalse($object->isValueEmpty('test.string'));
        $this->assertFalse($object->isValueNumeric('test.string'));
        $this->assertFalse($object->isValueFloat('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));

        $object->setValue('test.string', '0');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertfalse($object->isValueEmpty('test.string'));
        $this->assertTrue($object->isValueNumeric('test.string'));
        $this->assertTrue($object->isValueFloat('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));

        $object->setValue('test.string', '10');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertfalse($object->isValueEmpty('test.string'));
        $this->assertTrue($object->isValueNumeric('test.string'));
        $this->assertTrue($object->isValueFloat('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));

        $object->setValue('test.string', '22.659');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertfalse($object->isValueEmpty('test.string'));
        $this->assertTrue($object->isValueNumeric('test.string'));
        $this->assertTrue($object->isValueFloat('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));

        $object->setValue('test.guid', nb_generateGUID());
        $this->assertFalse($object->isValueNull('test.guid'));
        $this->assertfalse($object->isValueEmpty('test.guid'));
        $this->assertFalse($object->isValueNumeric('test.guid'));
        $this->assertFalse($object->isValueFloat('test.guid'));
        $this->assertTrue($object->isValueString('test.guid'));
        $this->assertFalse($object->isValueEmptyString('test.guid'));
        $this->assertTrue($object->isValueGUID('test.guid'));
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
