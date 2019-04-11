<?php

use PHPUnit\Framework\TestCase;

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

/**
 * PHPUnit tests to verify functionality of strings.php functions
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.0 Surface
 * @version 3.0.0 Surface
 * @package tests\nabu\min
 */
class CNabUStringFunctionsTest extends TestCase
{
    /**
     * @test ::nb_strStartsWith
     */
    public function testNbStrStartsWith()
    {
        $this->assertFalse(nb_strStartsWith(null, null));
        $this->assertFalse(nb_strStartsWith(null, 'anything'));
        $this->assertFalse(nb_strStartsWith('anything', null));
        $this->assertFalse(nb_strStartsWith('nb_', 'nb_str'));
        $this->assertFalse(nb_strStartsWith('nb_strStarts', 'mb_string'));
        $this->assertTrue(nb_strStartsWith('nb_strStartsWith test', 'nb_str'));
        $this->assertTrue(nb_strStartsWith('áccènt', 'ácc'));
        $this->assertFalse(nb_strStartsWith('áccènt', 'acc'));
        $this->assertFalse(nb_strStartsWith('accent', 'ácc'));
    }

    /**
     * @test ::nb_strEndsWith
     */
    public function testNbStrEndsWith()
    {
        $this->assertFalse(nb_strEndsWith(null, null));
        $this->assertFalse(nb_strEndsWith(null, 'anything'));
        $this->assertFalse(nb_strEndsWith('anything', null));
        $this->assertFalse(nb_strEndsWith('str', 'nb_str'));
        $this->assertFalse(nb_strEndsWith('nb_strEndsWith', 'mb_string'));
        $this->assertTrue(nb_strEndsWith('nb_strEndsWith test', 'test'));
        $this->assertTrue(nb_strEndsWith('áccènt', 'ènt'));
        $this->assertFalse(nb_strEndsWith('áccènt', 'ent'));
        $this->assertFalse(nb_strEndsWith('accent', 'ènt'));
    }
}
