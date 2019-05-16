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

namespace nabu\data\interfaces;

use Countable;
use SeekableIterator;

/**
 * Interface to implement iterable objects in nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.4
 * @package \nabu\data\interfaces
 */
interface INabuDataIterable extends Countable, SeekableIterator
{
    /**
     * Check if none values are stored.
     * @return bool Returns true if data storage is empty.
     */
    public function isEmpty(): bool;
    /**
     * Check if the list if filled.
     * @return bool Returns true if the list contains at least one item of false if is empty.
     */
    public function isFilled(): bool;
    /**
     * Empty the list and reset all indexes.
     * @return INabuDataList Return the self pointer to grant fluent interfaces.
     */
    public function clear(): INabuDataIterable;
}
