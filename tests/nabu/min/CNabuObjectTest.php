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

namespace tests\nabu\core;

use PHPUnit\Framework\TestCase;

use nabu\min\CNabuObject;

/**
 * PHPUnit tests to verify functionality of class @see CNabuObject
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.12 Surface
 * @version 3.0.12 Surface
 * @package tests\nabu\core
 */
class CNabuObjectTest extends TestCase
{
    /** @var int Number of iterations to test GUID massive tests. */
    private const GUID_LOOP_COUNT = 1000;

    /**
     * @test getTimestamp
     */
    public function testGetTimestamp()
    {
        $start_time = time();
        $nb_object = new CNabuObject();
        $current_time = $nb_object->getTimestamp();
        $max_time = time();

        $this->assertGreaterThanOrEqual($start_time, $current_time);
        $this->assertLessThanOrEqual($max_time, $current_time);
    }

    /**
     * @test isBuiltIn
     */
    public function testIsBuiltIn()
    {
        $nb_object = new CNabuObject();

        $this->assertFalse($nb_object->isBuiltIn());
    }

    /**
     * @test createHash
     * @test getHash
     * @test generateGUID
     * @test isValidGUID
     */
    public function testCreateAndGetHash_1()
    {
        $nb_object = new CNabuObject();

        $hash = $nb_object->createHash();
        $this->assertTrue(CNabuObject::isValidGUID($hash));

        $hash = $nb_object->getHash();
        $this->assertTrue(CNabuObject::isValidGUID($hash));

        $this->assertFalse(CNabuObject::isValidGUID('test-guid-invalid'));
    }

    /**
     * @test getHash
     * @test isValidGUID
     */
    public function testCreateAndGetHash_2()
    {
        $nb_object = new CNabuObject();

        $hash = $nb_object->getHash();
        $this->assertTrue(CNabuObject::isValidGUID($hash));
    }

    /**
     * @test generateGUID
     * @test isValidGUID
     */
    public function testMassiveGUIDManagement()
    {
        for ($i = 0; $i < self::GUID_LOOP_COUNT && CNabuObject::isValidGUID(CNabuObject::generateGUID()); $i++);
        $this->assertSame(self::GUID_LOOP_COUNT, $i);
    }
}
