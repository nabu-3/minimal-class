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

namespace tests\nabu\min;

use PHPUnit\Framework\TestCase;

use nabu\min\exceptions\ENabuException;

/**
 * PHPUnit tests to verify functionality of class @see ENabuException
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.0 Surface
 * @version 3.0.0 Surface
 * @package tests\nabu\min
 */
class CNabuExceptionTest extends TestCase
{
    /**
     * @test __construct
     */
    public function testConstruct()
    {
        $this->expectException(ENabuExceptionAux::class);
        throw new ENabuExceptionAux('Exception test message', 1);
    }
}

/**
 * Class defined for convenience to test @see CNabuException abstract class.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.12 Surface
 * @version 3.0.12 Surface
 * @package tests\nabu\min
 */
class ENabuExceptionAux extends ENabuException
{

}
