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

use nabu\data\interfaces\INabuDataList;
use nabu\data\interfaces\INabuDataReadable;
use nabu\data\interfaces\INabuDataListIndex;
use nabu\data\interfaces\INabuDataIndexedList;

/**
 * Abstract class to implement indexed lists of data objects of nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.3
 * @package \nabu\data
 */
abstract class CNabuDataIndexedList extends CNabuDataList implements INabuDataIndexedList
{
    /** @var string Index Pointer field name literal value. */
    private const INDEX_POINTER = 'pointer';

    /** @var array Associative array which includes all defined secondary indexes. */
    private $secondary_indexes = false;

    /**
     * Creates the instance and initiates the secondary index list.
     * @param string $index_field Field index to be used for main indexation.
     */
    public function __construct(string $index_field)
    {
        parent::__construct();

        $this->createSecondaryIndexes();
    }

    public function clear(): INabuDataIndexedList
    {
        parent::clear();

        if (is_array($this->secondary_indexes)) {
            foreach ($this->secondary_indexes as $index) {
                $index->clear();
            }
        }

        return $this;
    }

    public function getKeys(string $index = null): ?array
    {
        $retval = null;

        if (is_null($index)) {
            $retval = parent::getKeys();
        } else {
            $secondary_index = $this->getSecondaryIndex($index);
            $retval = $secondary_index->getKeys();
        }

        return $retval;
    }

    public function hasKey($key, ?string $index): bool
    {
        $retval = false;

        if (is_scalar($key)) {
            if (is_null($index)) {
                $retval = parent::hasKey($key);
            } else {
                $secondary_index = $this->getSecondaryIndex($index);
                $retval = $secondary_index->hasKey($key);
            }
        }

        return $retval;
    }

    public function addItem(INabuDataReadable $item): INabuDataReadable
    {
        $retval = parent::addItem($item);

        if (is_array($this->secondary_indexes) && ($retval instanceof INabuDataReadable)) {
            foreach ($this->secondary_indexes as $index) {
                $index->addItem($item);
            }
        }

        return $retval;
    }

    public function getItem(string $key, ?string $index): ?INabuDataReadable
    {
        $retval = null;

        if (is_null($index)) {
            $retval = parent::getIndex($key);
        } else {
            $retval = $this->getItemInternal($key, $index);
        }

        return $retval;
    }

    public function removeItem($item): ?INabuDataReadable
    {
        $retval = null;

        $nb_index_id = nb_getMixedValue($item, $this->index_field);
        if (!is_null($nb_index_id) && $this->hasKey($nb_index_id)) {
            $retval = parent::removeItem($nb_index_id);
            if ($retval instanceof INabuDataReadable && is_array($this->secondary_indexes)) {
                foreach ($this->secondary_indexes as $index) {
                    $index->removeItem($nb_index_id);
                }
            }
        }

        return $retval;
    }

    /**
     * Private method to get an Item as part of a Secondary Index.
     * @param string $key Id of searched instance.
     * @param string $index Specifies the Secondary Index to use.
     * @return INabuDataReadable|null Returns the instance indexed by $key in selected index or null if not exists.
     */
    private function getItemInternal(string $key, string $index): ?INabuDataReadable
    {
        $list_index = $this->getSecondaryIndex($index);
        if (($pointer = $list_index->getItemPointer($key)) &&
            array_key_exists(self::INDEX_POINTER, $pointer) &&
            $this->hasKey($pointer[self::INDEX_POINTER])
        ) {
            $retval = $this->list[$pointer[self::INDEX_POINTER]];
            if (is_null($retval)) {
                $retval = $this->acquireItem($key, $index);
                ($retval instanceof INabuDataReadable) && $this->addItem($retval);
            }
        }

        return $retval;
    }

    /**
     * Get a SecondaryIndex instance inernally. If desired index not exists triggers an User error.
     * @param string $index Index name to get.
     * @return INabuDataListIndex Index instance found.
     */
    private function getSecondaryIndex(string $index): INabuDataListIndex
    {
        if (is_array($this->secondary_indexes) &&
            array_key_exists($index, $this->secondary_indexes)
        ) {
            return $this->secondary_indexes[$index];
        } else {
            trigger_error(sprintf(TRIGGER_ERROR_INVALID_INDEX, $index));
        }
    }
}
