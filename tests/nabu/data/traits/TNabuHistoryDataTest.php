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

namespace nabu\data\traits;

use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\Error\Notice;

use PHPUnit\Framework\TestCase;

use nabu\data\CNabuDataObject;
use nabu\data\CNabuRODataObject;

/**
 * PHPUnit tests to verify functionality of class @see { TNabuHistoryData }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package nabu\data\traits
 */
class TNabuHistoryDataTest extends TestCase
{
    /**
     * @test resetHistory
     * @test isHistoryEmpty
     * @test push
     * @test pop
     */
    public function testResetHistoryWR()
    {
        $object = new CNabuHistoryDataTestingWR();
        $this->assertTrue($object->isEmpty());
        $this->assertTrue($object->isHistoryEmpty());
        $this->assertFalse($object->reset());
        $this->assertFalse($object->resetHistory());
        $object->setValue('test_name', 'test_value');
        $this->assertTrue($object->push());
        $this->assertFalse($object->isEmpty());
        $this->assertFalse($object->isHistoryEmpty());
        $this->assertTrue($object->resetHistory());
        $this->assertTrue($object->isEmpty());
        $this->assertTrue($object->isHistoryEmpty());
    }

    /**
     * @test resetHistory
     * @test isHistoryEmpty
     */
    public function testResetHistoryRO()
    {
        $object = new CNabuHistoryDataTestingRO();
        $this->assertTrue($object->isEmpty());
        $this->assertTrue($object->isHistoryEmpty());
        $this->expectException(Error::class);
        $object->resetHistory();
    }

    /**
     * @test push
     * @test pop
     */
    public function testPushAndPopWR()
    {
        $object = new CNabuHistoryDataTestingWR();
        $this->assertTrue($object->isHistoryEmpty());
        $this->assertFalse($object->push());
        $object->setValue('test_name', 'test_value');
        $this->assertTrue($object->push());
        $object->setValue('test_name', 'another_value');
        $this->assertTrue($object->push());
        $this->assertSame('another_value', $object->getValue('test_name'));
        $this->assertTrue($object->pop());
        $this->assertSame('another_value', $object->getValue('test_name'));
        $this->assertTrue($object->pop());
        $this->assertSame('test_value', $object->getValue('test_name'));

        $object->setAsReadOnly();
        $this->expectException(Error::class);
        $object->push();
    }

    /**
     * @test push
     */
    public function testPushRO()
    {
        $object = new CNabuHistoryDataTestingRO();
        $this->assertTrue($object->isHistoryEmpty());
        $this->expectException(Error::class);
        $object->push();
    }

    /**
     * @test pop
     */
    public function testPopWhenStackEmptyWR()
    {
        $object = new CNabuHistoryDataTestingWR();

        $this->expectException(Notice::class);
        $object->pop();
    }

    /**
     * @test pop
     */
    public function testPopWhenReadOnlyWR()
    {
        $object = new CNabuHistoryDataTestingWR();
        $object->setAsReadOnly();
        $this->assertTrue($object->isReadOnly());

        $this->expectException(Error::class);
        $object->pop();
    }

    /**
     * @test pop
     */
    public function testPopRO()
    {
        $object = new CNabuHistoryDataTestingWR();
        $this->assertTrue($object->isHistoryEmpty());
        $this->expectException(Error::class);
        $object->pop();
    }

    /**
     * @test overwrite
     */
    public function testOverwriteWR()
    {
        $object = new CNabuHistoryDataTestingWR();
        $this->assertTrue($object->isHistoryEmpty());
        $this->assertFalse($object->overwrite());
        $object->setValue('test_name', 'test_value');
        $this->assertTrue($object->push());
        $object->setValue('test_name', 'another_value');
        $this->assertTrue($object->overwrite());
        $this->assertSame('another_value', $object->getValue('test_name'));
        $object->setValue('test_name', 'more_values');
        $this->assertTrue($object->push());
        $this->assertSame('more_values', $object->getValue('test_name'));
        $this->assertTrue($object->pop());
        $this->assertSame('more_values', $object->getValue('test_name'));
        $this->assertTrue($object->pop());
        $this->assertSame('another_value', $object->getValue('test_name'));

        $this->expectException(Error::class);
        $object->pop();
    }

    /**
     * @test overwrite
     */
    public function testOverwriteReadOnlyWR()
    {
        $object = new CNabuHistoryDataTestingWR();
        $this->assertTrue($object->isEditable());
        $object->setValue('test_name', 'test_value');
        $object->push();
        $object->setAsReadOnly();
        $this->assertTrue($object->isReadOnly());
        $this->expectException(Error::class);
        $object->overwrite();
    }

    /**
     * @test overwrite
     */
    public function testOverwriteRO()
    {
        $object = new CNabuHistoryDataTestingRO();
        $this->assertTrue($object->isHistoryEmpty());
        $this->expectException(Error::class);
        $object->overwrite();
    }
}

class CNabuHistoryDataTestingWR extends CNabuDataObject
{
    use TNabuHistoryData;
}

class CNabuHistoryDataTestingRO extends CNabuRODataObject
{
    use TNabuHistoryData;
}
