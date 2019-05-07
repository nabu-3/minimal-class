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

use PHPUnit\Framework\Error\Error;

use PHPUnit\Framework\TestCase;

use nabu\data\CNabuDataObject;
use nabu\data\CNabuRODataObject;

/**
 * PHPUnit tests to verify functionality of trait @see { TNabuNestedData }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package nabu\data\traits
 */
class TNabuNestedDataTest extends TestCase
{
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
     * @test removeValue
     * @test renameValue
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
        $this->assertTrue($object->isValueArray('c'));
        $this->assertFalse($object->isValueArray('c.d'));

        $this->assertTrue($object->grantPath('c.d.e'));
        $this->assertTrue($object->hasValue('c.d'));
        $this->assertTrue($object->hasValue('c.d.e'));
        $this->assertSame(array('d' => array('e' => null)), $object->getValue('c'));
        $this->assertSame(array('e' => null), $object->getValue('c.d'));
        $this->assertTrue($object->isValueArray('c'));
        $this->assertTrue($object->isValueArray('c.d'));
        $this->assertFalse($object->isValueArray('c.d.e'));
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
        $this->assertTrue($object->isValueArray('b'));
        $this->assertTrue($object->isValueArray('b.x'));
        $this->assertFalse($object->isValueArray('b.x.z'));
        $this->assertTrue($object->hasValue('a'));
        $this->assertTrue($object->hasValue('b'));
        $this->assertTrue($object->isValueNull('a'));
        $this->assertFalse($object->isValueNull('b'));
        $this->assertFalse($object->isValueNull('c'));
        $this->assertFalse($object->isValueNull('c.d'));
        $this->assertSame(array('d' => array('e' => null)), $object->getValue('c'));
        $this->assertSame(array('e' => null), $object->getValue('c.d'));
        $this->assertTrue($object->isValueNull('c.d.e'));

        $this->assertSame($object, $object->removeValue('c.d.e'));
        $this->assertTrue($object->hasValue('c.d'));
        $this->assertFalse($object->hasValue('c.d.e'));
        $this->assertSame($object, $object->removeValue('c'));
        $this->assertFalse($object->hasValue('c'));
        $object->with('b.x');
        $this->assertSame($object, $object->removeValue('z'));
        $this->assertFalse($object->hasValue('z'));
        $object->with();
        $this->assertTrue($object->hasValue('b.x'));
        $this->assertFalse($object->hasValue('b.x.z'));

        $this->assertSame($object, $object->renameValue('b.x', 'k.x'));
        $this->assertFalse($object->hasValue('b.x'));
        $this->assertTrue($object->hasValue('k.x'));
        $object->with('k');
        $this->assertSame($object, $object->renameValue('x', 'm'));
        $this->assertTrue($object->hasValue('m'));
        $this->assertFalse($object->hasValue('x'));
        $object->with();
        $this->assertTrue($object->hasValue('k.m'));
        $this->assertFalse($object->hasValue('k.x'));
        $this->assertSame($object, $object->renameValue('k.m', 'k.z'));
        $this->assertTrue($object->hasValue('k.z'));
        $this->assertFalse($object->hasValue('k.m'));
    }

    /**
     * @test setValue
     * @test hasValue
     */
    public function testSetValueRO()
    {
        $object = new CNabuNestedDataTestingRO();
        $this->expectException(Error::class);
        $this->expectExceptionMessage(TRIGGER_ERROR_READ_ONLY_MODE);
        $object->setValue('a', 1);
    }

    /**
     * @test setValue
     * @test removeValue
     * @test hasValue
     */
    public function testRemoveValueRO()
    {
        $object = new CNabuNestedDataTestingRO(array(
            'a' => 1
        ));
        $this->expectException(Error::class);
        $this->expectExceptionMessage(TRIGGER_ERROR_READ_ONLY_MODE);
        $object->removeValue('a');
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
        $object->setValue('g.h.i', true);
        $this->assertTrue($object->hasValue('g.h.i'));
        $this->assertTrue($object->isValueEqualTo('g.h.i', true));
    }

    /**
     * @test CNabuRODataObject::isValueNull
     * @test CNabuRODataObject::isValueEmpty
     * @test CNabuRODataObject::isValueNumeric
     * @test CNabuRODataObject::isValueFloat
     * @test CNabuRODataObject::isValueBool
     * @test CNabuRODataObject::isValueString
     * @test CNabuRODataObject::isValueEmptyString
     * @test CNabuRODataObject::isValueGUID
     * @test CNabuRODataObject::isValueArray
     * @test CNabuRODataObject::isValueEmptyArray
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
        $this->assertFalse($object->isValueBool('test.null'));
        $this->assertFalse($object->isValueString('test.null'));
        $this->assertFalse($object->isValueEmptyString('test.null'));
        $this->assertFalse($object->isValueGUID('test.null'));
        $this->assertFalse($object->isValueArray('test.null'));
        $this->assertFalse($object->isValueEmptyArray('test.null'));

        $object->setValue('test.numeric', 0);
        $this->assertFalse($object->isValueNull('test.numeric'));
        $this->assertTrue($object->isValueEmpty('test.numeric'));
        $this->assertTrue($object->isValueNumeric('test.numeric'));
        $this->assertTrue($object->isValueFloat('test.numeric'));
        $this->assertFalse($object->isValueBool('test.numeric'));
        $this->assertFalse($object->isValueString('test.numeric'));
        $this->assertFalse($object->isValueEmptyString('test.numeric'));
        $this->assertFalse($object->isValueGUID('test.numeric'));
        $this->assertFalse($object->isValueArray('test.numeric'));
        $this->assertFalse($object->isValueEmptyArray('test.numeric'));

        $object->setValue('test.numeric', 10);
        $this->assertFalse($object->isValueNull('test.numeric'));
        $this->assertFalse($object->isValueEmpty('test.numeric'));
        $this->assertTrue($object->isValueNumeric('test.numeric'));
        $this->assertTrue($object->isValueFloat('test.numeric'));
        $this->assertFalse($object->isValueBool('test.numeric'));
        $this->assertFalse($object->isValueString('test.numeric'));
        $this->assertFalse($object->isValueEmptyString('test.numeric'));
        $this->assertFalse($object->isValueGUID('test.numeric'));
        $this->assertFalse($object->isValueArray('test.numeric'));
        $this->assertFalse($object->isValueEmptyArray('test.numeric'));

        $object->setValue('test.float', 10.5);
        $this->assertFalse($object->isValueNull('test.float'));
        $this->assertFalse($object->isValueEmpty('test.float'));
        $this->assertTrue($object->isValueNumeric('test.float'));
        $this->assertTrue($object->isValueFloat('test.float'));
        $this->assertFalse($object->isValueBool('test.float'));
        $this->assertFalse($object->isValueString('test.float'));
        $this->assertFalse($object->isValueEmptyString('test.float'));
        $this->assertFalse($object->isValueGUID('test.float'));
        $this->assertFalse($object->isValueArray('test.float'));
        $this->assertFalse($object->isValueEmptyArray('test.float'));

        $object->setValue('test.bool', false);
        $this->assertFalse($object->isValueNull('test.bool'));
        $this->assertTrue($object->isValueEmpty('test.bool'));
        $this->assertFalse($object->isValueNumeric('test.bool'));
        $this->assertFalse($object->isValueFloat('test.bool'));
        $this->assertTrue($object->isValueBool('test.bool'));
        $this->assertFalse($object->isValueString('test.bool'));
        $this->assertFalse($object->isValueEmptyString('test.bool'));
        $this->assertFalse($object->isValueGUID('test.bool'));
        $this->assertFalse($object->isValueArray('test.bool'));
        $this->assertFalse($object->isValueEmptyArray('test.bool'));

        $object->setValue('test.bool', true);
        $this->assertFalse($object->isValueNull('test.bool'));
        $this->assertFalse($object->isValueEmpty('test.bool'));
        $this->assertFalse($object->isValueNumeric('test.bool'));
        $this->assertFalse($object->isValueFloat('test.bool'));
        $this->assertTrue($object->isValueBool('test.bool'));
        $this->assertFalse($object->isValueString('test.bool'));
        $this->assertFalse($object->isValueEmptyString('test.bool'));
        $this->assertFalse($object->isValueGUID('test.bool'));
        $this->assertFalse($object->isValueArray('test.bool'));
        $this->assertFalse($object->isValueEmptyArray('test.bool'));

        $object->setValue('test.string', '');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertTrue($object->isValueEmpty('test.string'));
        $this->assertFalse($object->isValueNumeric('test.string'));
        $this->assertFalse($object->isValueFloat('test.string'));
        $this->assertFalse($object->isValueBool('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertTrue($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));
        $this->assertFalse($object->isValueArray('test.string'));
        $this->assertFalse($object->isValueEmptyArray('test.string'));

        $object->setValue('test.string', 'test value');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertFalse($object->isValueEmpty('test.string'));
        $this->assertFalse($object->isValueNumeric('test.string'));
        $this->assertFalse($object->isValueFloat('test.string'));
        $this->assertFalse($object->isValueBool('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));
        $this->assertFalse($object->isValueArray('test.string'));
        $this->assertFalse($object->isValueEmptyArray('test.string'));

        $object->setValue('test.string', 'null');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertFalse($object->isValueEmpty('test.string'));
        $this->assertFalse($object->isValueNumeric('test.string'));
        $this->assertFalse($object->isValueFloat('test.string'));
        $this->assertFalse($object->isValueBool('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));
        $this->assertFalse($object->isValueArray('test.string'));
        $this->assertFalse($object->isValueEmptyArray('test.string'));

        $object->setValue('test.string', 'false');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertFalse($object->isValueEmpty('test.string'));
        $this->assertFalse($object->isValueNumeric('test.string'));
        $this->assertFalse($object->isValueFloat('test.string'));
        $this->assertFalse($object->isValueBool('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));
        $this->assertFalse($object->isValueArray('test.string'));
        $this->assertFalse($object->isValueEmptyArray('test.string'));

        $object->setValue('test.string', '0');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertFalse($object->isValueEmpty('test.string'));
        $this->assertTrue($object->isValueNumeric('test.string'));
        $this->assertTrue($object->isValueFloat('test.string'));
        $this->assertFalse($object->isValueBool('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));
        $this->assertFalse($object->isValueArray('test.string'));
        $this->assertFalse($object->isValueEmptyArray('test.string'));

        $object->setValue('test.string', '10');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertFalse($object->isValueEmpty('test.string'));
        $this->assertTrue($object->isValueNumeric('test.string'));
        $this->assertTrue($object->isValueFloat('test.string'));
        $this->assertFalse($object->isValueBool('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));
        $this->assertFalse($object->isValueArray('test.string'));
        $this->assertFalse($object->isValueEmptyArray('test.string'));

        $object->setValue('test.string', '22.659');
        $this->assertFalse($object->isValueNull('test.string'));
        $this->assertFalse($object->isValueEmpty('test.string'));
        $this->assertTrue($object->isValueNumeric('test.string'));
        $this->assertTrue($object->isValueFloat('test.string'));
        $this->assertFalse($object->isValueBool('test.string'));
        $this->assertTrue($object->isValueString('test.string'));
        $this->assertFalse($object->isValueEmptyString('test.string'));
        $this->assertFalse($object->isValueGUID('test.string'));
        $this->assertFalse($object->isValueArray('test.string'));
        $this->assertFalse($object->isValueEmptyArray('test.string'));

        $object->setValue('test.guid', nb_generateGUID());
        $this->assertFalse($object->isValueNull('test.guid'));
        $this->assertFalse($object->isValueEmpty('test.guid'));
        $this->assertFalse($object->isValueNumeric('test.guid'));
        $this->assertFalse($object->isValueFloat('test.guid'));
        $this->assertFalse($object->isValueBool('test.string'));
        $this->assertTrue($object->isValueString('test.guid'));
        $this->assertFalse($object->isValueEmptyString('test.guid'));
        $this->assertTrue($object->isValueGUID('test.guid'));
        $this->assertFalse($object->isValueArray('test.guid'));
        $this->assertFalse($object->isValueEmptyArray('test.guid'));

        $object->setValue('test.array', array());
        $this->assertFalse($object->isValueNull('test.array'));
        $this->assertTrue($object->isValueEmpty('test.array'));
        $this->assertFalse($object->isValueNumeric('test.array'));
        $this->assertFalse($object->isValueFloat('test.array'));
        $this->assertFalse($object->isValueBool('test.array'));
        $this->assertFalse($object->isValueString('test.array'));
        $this->assertFalse($object->isValueEmptyString('test.array'));
        $this->assertFalse($object->isValueGUID('test.array'));
        $this->assertTrue($object->isValueArray('test.array'));
        $this->assertTrue($object->isValueEmptyArray('test.array'));

        $object->setValue('test.array', array('a' => 1, 'b' => 2));
        $this->assertFalse($object->isValueNull('test.array'));
        $this->assertFalse($object->isValueEmpty('test.array'));
        $this->assertFalse($object->isValueNumeric('test.array'));
        $this->assertFalse($object->isValueFloat('test.array'));
        $this->assertFalse($object->isValueBool('test.array'));
        $this->assertFalse($object->isValueString('test.array'));
        $this->assertFalse($object->isValueEmptyString('test.array'));
        $this->assertFalse($object->isValueGUID('test.array'));
        $this->assertTrue($object->isValueArray('test.array'));
        $this->assertFalse($object->isValueEmptyArray('test.array'));
    }

    /**
     * @test setValue
     * @test hasValue
     * @test grantPath
     */
    public function testCombinedArrayAndNestedMethods()
    {
        $object = new CNabuNestedDataTestingWR();
        $this->assertTrue($object->isEditable());

        $object->grantPath('a.b.c');
        $this->assertTrue($object->hasValue('a.b.c'));

        $object->setValue('d', array('e' => array('f' => null)));
        $this->assertTrue($object->hasValue('d.e.f'));

        $object->setValue('d.e.g', 'check');
        $this->assertTrue($object->hasValue('d.e.f'));
        $this->assertTrue($object->hasValue('d.e.g'));

        $object->setValue('h.i', array('v-1', 'v2', 'v-3'));
        $this->assertTrue($object->hasValue('h.i.0'));
        $this->assertTrue($object->isValueEqualTo('h.i.0', 'v-1'));
        $this->assertTrue($object->hasValue('h.i.1'));
        $this->assertTrue($object->isValueEqualTo('h.i.1', 'v2'));
        $this->assertTrue($object->hasValue('h.i.2'));
        $this->assertTrue($object->isValueEqualTo('h.i.2', 'v-3'));

        $object->setValue('h.i.j', 'v-4');
        $this->assertTrue($object->hasValue('h.i.j'));
        $this->assertTrue($object->isValueEqualTo('h.i.j', 'v-4'));
    }

    /**
     * @test with
     * @test getWithPreffix
     * @test translatePath
     * @test getValue
     * @test hasValue
     * @test setValue
     * @test grantPath
     */
    public function testWidth()
    {
        $object = new CNabuNestedDataTestingWR();
        $this->assertTrue($object->isEditable());

        $object->grantPath('a.b');
        $this->assertTrue($object->hasValue('a.b'));

        $object->with('a.b.c')->setValue('d', 25)->with();
        $this->assertSame(25, $object->getValue('a.b.c.d'));
        $this->assertNull($object->getWithPreffix());

        $object->with('a.b.c')->grantPath('e');
        $this->assertTrue($object->hasValue('e'));
        $object->setValue('e', 80);
        $this->assertSame(80, $object->getValue('e'));
        $this->assertSame('a.b.c', $object->getWithPreffix());
        $object->with();
        $this->assertNull($object->getWithPreffix());
        $this->assertTrue($object->hasValue('a.b.c.e'));
        $this->assertSame(80, $object->getValue('a.b.c.e'));
        $this->assertSame(25, $object->getValue('a.b.c.d'));
        $object->with('a.b')->setValue('f.g', 48)->with();
        $this->assertNull($object->getWithPreffix());
        $this->assertTrue($object->hasValue('a.b.f.g'));
        $this->assertSame(48, $object->getValue('a.b.f.g'));
        $this->assertTrue($object->grantPath('a.b.c.d'));
        $this->assertTrue($object->grantPath('a.b.c.e'));
        $this->assertTrue($object->grantPath('a.b.f.g'));

        $this->assertSame(
            array(
                'a' => array(
                    'b' => array(
                        'c' => array(
                            'd' => 25,
                            'e' => 80
                        ),
                        'f' => array(
                            'g' => 48
                        )
                    )
                )
            ),
            $object->getValuesAsArray()
        );
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
