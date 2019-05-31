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

require_once 'vendor/autoload.php';

use nabu\data\CNabuAbstractDataList;
use nabu\data\CNabuAbstractDataObject;
use nabu\data\interfaces\INabuDataReadable;

class CNabuAbstractDataListSample01 extends CNabuAbstractDataList
{
    protected function acquireItem($key): ?INabuDataReadable
    {
        return null;
    }

    protected function createDataInstance(array $data): ?INabuDataReadable
    {
        return new CNabuAbstractDataListObjectSample01($data);
    }
}

class CNabuAbstractDataListObjectSample01 extends CNabuAbstractDataObject
{

}

$arrobj = array();
$arrobj2 = array();

for ($i = 1; $i < 11; $i++) {
    $arrobj[$i - 1] = array(
        'key_field' => $i,
        'key_value' => "value $i"
    );
    $arrobj2[$i + 10] = array(
        'key_field' => $i + 10,
        'key_value' => 'value ' . ($i + 10)
    );
}

echo 'Array count: ' . count($arrobj) . "\n";

$list = new CNabuAbstractDataListSample01('key_field', $arrobj);
echo 'List count: ' . count($list);
