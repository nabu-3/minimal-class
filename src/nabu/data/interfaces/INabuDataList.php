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
 * Interface to implement lists of data objects implementing @see { INabuDataReadable } interface in nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.4
 * @package \nabu\data\interfaces
 */
interface INabuDataList extends INabuDataIterable
{
    /**
     * Creates the instance.
     * @param string|null $index_field Field index to be used for main indexation.
     * @param mixed|null $source_list Another INabuDataList instance or an array to copy the list.
     */
    public function __construct(?string $index_field = null, $source_list = null);
    /**
     * Gets the main Indexed Field Name.
     * @return string|null Returns the name of indexed field.
     */
    public function getMainIndexFieldName(): ?string;
    /**
     * Gets keys of this index as an array.
     * @param string|null $index Alternate index to get keys.
     * @return array|null Returns the array of keys if the list is filled or null if the list is empty.
     */
    public function getKeys(?string $index = null): ?array;
    /**
     * Gets all items of the list as an array.
     * @return array|null Returns the array of items or null if the list is empty.
     */
    public function getItems(): ?array;
    /**
     * Check if a key exists.
     * @param mixed $key Key to check.
     * @param string|null $index Alternative index to be used.
     * @return bool Returns true if the key exists in the required index.
     */
    public function hasKey($key, ?string $index = null): bool;
    /**
     * Adds a new item to the list.
     * @param INabuDataReadable $item Object item to be added.
     * @param mixed|null $key Optional key to use to index the item when index field is not inside $item parameter.
     * @return INabuDataReadable Returns the inserted object instance.
     */
    public function addItem(INabuDataReadable $item, $key = null): INabuDataReadable;
    /**
     * Gets an item from the collection indexed by $key. If the list does not contain the item, calls internally
     * the protected method @see { acquireItem() } to retrieve the item from the storage.
     * @param string $key Id of searched instance.
     * @param string|null $index If null then uses the main index and otherwise specifies an alternate index to use.
     * @return INabuDataReadable|null Returns the instance indexed by $key in selected index or null if not exists.
     */
    public function getItem(string $key, ?string $index = null): ?INabuDataReadable;
    /**
     * Removes an item from the list.
     * @param mixed $item An INabuDataReadable instance containing a field matching the main index field name
     * or a scalar variable containing the Id to be removed.
     * @return INabuDataReadable|null Returns the removed item if exists or null if not.
     */
    public function removeItem($item): ?INabuDataReadable;
    /**
     * Merges another list of items in this list.
     * @param INabuDataList|null $list List to be merged. If null, then the list is ignored and returns 0 as result.
     * @return int Retuns the count of items merged.
     */
    public function merge(?INabuDataList $list): int;
    /**
     * Merges a list of items included in an array.
     * @param array|null $array Array of items to merge. Null value is allowed and returns 0 as result.
     * @return int Returns the count of items merged.
     */
    public function mergeArray(?array $array): int;
}
