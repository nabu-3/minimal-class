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
 * Interface to implement secodary list index in @see { INabuDataList } inherited classes in nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.3
 * @package \nabu\data\interfaces
 */
interface INabuDataListIndex
{
    /**
     * Creates the instance.
     * @param INabuDataIndexedList $list List parent of this index.
     * @param string $key_field Key field name to build the index.
     * @param string|null $key_order Field used to order the index.
     * @param string|null $name Name of the index.
     */
    public function __construct(INabuDataIndexedList $list, string $key_field, ?string $key_order, ?string $name);
    /**
     * Gets the name of the index.
     * @return string|null Returns the name.
     */
    public function getName(): ?string;
    /**
     * Gets the field index name.
     * @return string Returns the field index name.
     */
    public function getIndexedFieldName(): string;
    /**
     * Gets keys of this index as an array.
     * @return array|null Returns the array of keys if the list is filled or null if the list is empty.
     */
    public function getKeys(): ?array;
    /**
     * Check if the index is empty.
     * @return bool Return true if the index is empty.
     */
    public function isEmpty(): bool;
    /**
     * Check if the index have pointers.
     * @return bool Return true if the index have pointers.
     */
    public function isFilled(): bool;
    /**
     * Clear the index.
     */
    public function clear(): void;
    /**
     * Check if a key exists.
     * @param mixed $key Key to check.
     * @return bool Returns true if the key exists in the required index.
     */
    public function hasKey($key): bool;
    /**
     * Adds a new item to the index.
     * @param INabuDataReadable $item item to be added.
     * @return bool Returns true if the item added.
     */
    public function addItem(INabuDataReadable $item): bool;
    /**
     * Get the Pointer to an Item in this index identified by an Index Key.
     * @param string $key Key to search in index.
     * @return array|null Returns an array with the result or null if key does not match.
     */
    public function getItemPointer(string $key): ?array;
    /**
     * Removes an item from the list.
     * @param string $key The key value of the item to be removed.
     * @return bool Returns true if the item was removed.
     */
    public function removeItem(string $key): bool;

}
