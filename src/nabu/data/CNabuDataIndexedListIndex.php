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

use nabu\data\interfaces\INabuDataReadable;
use nabu\data\interfaces\INabuDataListIndex;
use nabu\data\interfaces\INabuDataIndexedList;

use nabu\min\CNabuObject;

/**
 * Abstract class to implement indexed lists of data objects of nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.3
 * @package \nabu\data
 */
class CNabuDataIndexedListIndex extends CNabuObject implements INabuDataListIndex
{
    /** @var INabuDataIndexedList|null List instance that owns this index. */
    protected $list = null;
    /** @var string|null Name of the index. */
    protected $name = null;
    /** @var string|null Field name to be indexed. */
    protected $key_field = null;
    /** @var string|null Field name of field to order the index. When null, ordering uses the same field
     * as index field. */
    protected $order_field = null;
    /** @var array|null Array collection of index nodes. */
    protected $index = null;

    public function __construct(INabuDataIndexedList $list, string $key_field, ?string $key_order, ?string $name)
    {
        $this->list = $list;
        $this->key_field = $key_field;
        $this->order_field = $key_order;
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIndexedFieldName(): string
    {
        return $this->key_field;
    }

    public function getKeys(): ?array
    {
        return $this->index;
    }

    public function isEmpty(): bool
    {
        return !is_array($this->index) || count($this->index) === 0;
    }

    public function isFilled(): bool
    {
        return is_array($this->index) && count($this->index) > 0;
    }

    public function clear(): void
    {
        $this->index = null;
    }

    public function hasKey($key): bool
    {
        return $this->isFilled() && array_key_exists($key, $this->index);
    }

    public function addItem(INabuDataReadable $item): bool
    {
        $retval = false;

        if (is_array($nodes = $this->extractNodes($item)) && count($nodes) > 0) {
            if ($this->index === null) {
                $this->index = $nodes;
            } else {
                $this->index = array_merge($this->index, $nodes);
            }
            $retval = true;
        }

        return $retval;
    }

    public function getItemPointer(string $key): ?array
    {
        return ($this->hasKey($key) ? $this->index[$key] : null);
    }

    public function removeItem(string $key): bool
    {
        $retval = false;

        if ($this->hasKey($key)) {
            unset($this->index[$key]);
            $retval = true;
        }

        return $retval;
    }

    /**
     * Extract Nodes list for an item in this node.
     * This method can be overrided in child classes to change the extraction method of nodes.
     * @param INabuDataReadable $item Item of which will extract the nodes.
     * @return array|null Returns an array of found nodes or null when they are not available nodes.
     */
    protected function extractNodes(INabuDataReadable $item): ?array
    {
        $main_index_name = $this->list->getMainIndexFieldName();
        if (($item->isValueNumeric($main_index_name) || $item->isValueGUID($main_index_name)) &&
            ($item->isValueString($this->key_field) || $item->isValueNumeric($this->key_field))
        ) {
            $key = $item->getValue($this->key_field);
            $retval = array(
                'key' => $key,
                'pointer' => $item->getValue($main_index_name)
            );
            if ($item->isValueNumeric($this->order_field) || $item->isValueString($this->order_field)) {
                $retval['order'] = $item->getValue($this->order_field);
            }
            $retval = array($key => $retval);
        } else {
            $retval = null;
        }

        return $retval;
    }
}
