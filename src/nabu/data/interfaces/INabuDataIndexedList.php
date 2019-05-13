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
 * @version 3.0.3
 * @package \nabu\data\interfaces
 */
interface INabuDataIndexedList extends INabuDataList
{
    /**
     * This method is called internally by getItem() or findByIndex() when the item does not exists
     * in the list. If we do not want to acquire/retrieve an object, only return null as result.
     * @param string $key Id of the item to be acquired.
     * @param string $index Secondary index to be used if needed.
     * @return INabuDataReadable|null Returns a @see { INabuDataReadable } instance if acquired or null if not.
     */
    protected function acquireItem($key, $index = false): ?INabuDataReadable;
    /**
     * Creates secondary indexes if needed.
     */
    protected function createSecondaryIndexes();
    /**
     * Gets keys of this index as an array.
     * @param string $index Alternate index to get keys.
     * @return array|null Returns the array of keys if the list is filled or null if the list is empty.
     */
    public function getKeys($index = false): ?array;
    /**
     * Check if a key exists.
     * @param mixed $key Key to check.
     * @param string|null $index Alternative index to be used.
     * @return bool Returns true if the key exists in the required index.
     */
    public function hasKey($key, ?string $index): bool;
    /**
     * Gets an item from the collection indexed by $key. If the list does not contain the item, calls internally
     * the protected method @see { acquireItem() } to retrieve the item from the storage.
     * @param string $key Id of searched instance.
     * @param string|null $index If null then uses the main index and otherwise specifies an alternate index to use.
     * @return INabuDataReadable|null Returns the instance indexed by $key in selected index or null if not exists.
     */
    public function getItem(string $key, ?string $index): ?INabuDataReadable;
}
