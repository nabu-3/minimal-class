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

/**
 * Interface to implement indexed lists of data objects implementing @see { INabuDataReadable } interface in nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.4
 * @package \nabu\data\interfaces
 */
interface INabuDataIndexedList extends INabuDataList
{
    /**
     * Adds a new index to have alternate indexes of this list.
     * @param INabuDataListIndex $index Instance to manage this index.
     * @return INabuDataListIndex Returns the $index added.
     */
    public function addSecondaryIndex(INabuDataListIndex $index): INabuDataListIndex;
    /**
     * Get a Secondary Index instance. If desired index not exists triggers an User error.
     * @param string $index Index name to get.
     * @return INabuDataListIndex Index instance found.
     */
    public function getSecondaryIndex(string $index): INabuDataListIndex;
    /**
     * Remove a Secondary Index instance. If desired index not exists triggers an User error.
     * @param string $index Index name to get.
     */
    public function removeSecondaryIndex(string $index): void;
}
