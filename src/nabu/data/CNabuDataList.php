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

use nabu\min\CNabuObject;

/**
 * Abstract class to implement lists of data objects of nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.3
 * @package \nabu\data
 */
abstract class CNabuDataList extends CNabuObject implements INabuDataList
{
    /** @var array|null Associative array containing all objects in the list. */
    protected $list = null;
    /** @var int Current Iterator position. */
    private $list_position = 0;
    /** @var string|null Main index field to index all objects in the primary list. */
    protected $index_field = null;

    /**
     * This method is called internally by getItem() or findByIndex() when the item does not exists
     * in the list. If we do not want to acquire/retrieve an object, only return null as result.
     * @param string $key Id of the item to be acquired.
     * @return INabuDataReadable|null Returns a @see { INabuDataReadable } instance if acquired or null if not.
     */
    abstract protected function acquireItem($key): ?INabuDataReadable;

    /**
     * Creates the instance.
     * @param string|null $index_field Field index to be used for main indexation.
     */
    public function __construct(?string $index_field = null)
    {
        parent::__construct();

        $this->index_field = $index_field;
        $this->rewind();
    }

    public function count()
    {
        return is_null($this->list) ? 0 : count($this->list);
    }

    public function current()
    {
        $current = null;

        if ($this->valid()) {
            $keys = array_keys($this->list);
            $current = $this->list[$keys[$this->list_position]];
        }

        return $current;
    }

    public function next()
    {
        $this->list_position++;
    }

    public function key()
    {
        $key = null;

        if ($this->valid()) {
            $keys = array_keys($this->list);
            $key = $keys[$this->list_position];
        }

        return $key;
    }

    public function valid()
    {
        $size = is_array($this->list) ? count($this->list) : 0;

        return $this->list_position < $size;
    }

    public function rewind()
    {
        $this->list_position = 0;
    }

    public function getMainIndexFieldName(): ?string
    {
        return $this->index_field;
    }

    public function isEmpty(): bool
    {
        return !is_array($this->list) || count($this->list) === 0;
    }

    public function isFilled(): bool
    {
        return is_array($this->list) && count($this->list) > 0;
    }

    public function clear(): INabuDataList
    {
        $this->list = null;
        $this->rewind();

        return $this;
    }

    public function getKeys(string $index = null): ?array
    {
        return is_array($this->list) ? array_keys($this->list) : null;
    }

    public function getItems(): ?array
    {
        return $this->list;
    }

    public function hasKey($key, ?string $index = null): bool
    {
        $retval = false;

        if (is_scalar($key)) {
            $retval = is_array($this->list) && array_key_exists($key, $this->list);
        } else {
            trigger_error(sprintf(TRIGGER_ERROR_INVALID_KEY, var_export($key, true)));
        }

        return $retval;
    }

    public function addItem(INabuDataReadable $item): INabuDataReadable
    {
        $retval = null;

        if (!is_null($this->index_field)) {
            if ($item->hasValue($this->index_field)) {
                if (is_array($this->list)) {
                    $this->list[$item->getValue($this->index_field)] = $item;
                    $retval = $item;
                } else {
                    $this->list = array(
                        $item->getValue($this->index_field) => $item
                    );
                    $retval = $item;
                }
            }
        } else {
            if (is_array($this->list)) {
                $this->list[] = $item;
                $retval = $item;
            } else {
                $this->list = array($item);
                $retval = $item;
            }
        }

        return $retval;
    }

    public function getItem(string $key, ?string $index = null): ?INabuDataReadable
    {
        $retval = null;

        if ($this->isFilled() && array_key_exists($key, $this->list)) {
            $retval = $this->list[$key];
        }

        if (is_null($retval)) {
            $retval = $this->acquireItem($key);
            ($retval instanceof INabuDataReadable) && $this->addItem($retval);
        }

        return $retval;
    }

    public function removeItem($item): ?INabuDataReadable
    {
        $retval = null;

        if (is_null($this->index_field)) {
            if (is_scalar($item) && $this->hasKey($item)) {
                $retval = $this->list[$item];
                unset($this->list[$item]);
            } elseif ($item instanceof INabuDataReadable && in_array($item, $this->list)) {
                $keys = array_keys($this->list, $item, true);
                foreach ($keys as $key) {
                    unset($this->list[$key]);
                }
                $retval = $item;
            }
        } else {
            $nb_index_id = nb_getMixedValue($item, $this->index_field);
            if (!is_null($nb_index_id) && $this->hasKey($nb_index_id)) {
                $retval = $this->list[$nb_index_id];
                unset($this->list[$nb_index_id]);
            }
        }

        return $retval;
    }

    public function merge(?INabuDataList $list): int
    {
        $count = 0;

        if ($this->isFilled() && $list->isFilled()) {
            foreach ($list as $key => $item) {
                if (!$this->hasKey($key)) {
                    $this->addItem($item);
                    $count++;
                }
            }
        } elseif ($this->isEmpty() && $list->isFilled()) {
            $this->list = $list->list;
            $count = is_array($list->list) ? count($list->list) : 0;
        }

        return $count;
    }

    public function mergeArray(?array $array): int
    {
        $count = 0;

        if (is_array($array) && count($array) > 0) {
            foreach ($array as $key => $item) {
                if (!$this->hasKey($key)) {
                    $this->addItem($item);
                    $count++;
                }
            }
        }

        return $count;
    }
}
