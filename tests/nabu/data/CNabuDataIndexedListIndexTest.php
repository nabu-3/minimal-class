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

use PHPUnit\Framework\TestCase;

use nabu\data\interfaces\INabuDataReadable;

/**
 * PHPUnit tests to verify functionality of class @see { CNabuDataList }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.3
 * @package nabu\data
 */
class CNabuDataIndexedListIndexTest extends TestCase
{
    /**
     * @test getIndexedFieldName
     * @test isEmpty
     */
    public function testMinimal()
    {
        $list = new CNabuDataIndexedListIndexTesting('key_field');
        $index = $list->getSecondaryIndex('secondary_index');
        $this->assertTrue($index->isEmpty());
        $this->assertSame('key_value', $index->getIndexedFieldName());
    }
}

class CNabuDataIndexedListIndexTesting extends CNabuDataIndexedList
{
    protected function acquireItem($key, ?string $index = null): ?INabuDataReadable
    {
        return null;
    }

    protected function createSecondaryIndexes(): void
    {
        $this->addSecondaryIndex(
            new CNabuDataIndexedListIndex($this, 'key_value', 'key_value', 'secondary_index')
        );
    }
}
