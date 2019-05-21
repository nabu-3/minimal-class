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

use InvalidArgumentException;
use UnexpectedValueException;

use nabu\data\interfaces\INabuDataList;
use nabu\data\interfaces\INabuDataIterable;
use nabu\data\interfaces\INabuDataReadable;

/**
 * Abstract class to implement lists of data objects of nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.4
 * @package \nabu\data
 */
abstract class CNabuDataList extends CNabuDataIterable implements INabuDataList
{
    /** @var string|null Main index field to index all objects in the primary list. */
    protected $index_field = null;
    /** @var string|null Child data class used to create child data programmatically. */

    /**
     * This method is called internally by getItem() or findByIndex() when the item does not exists
     * in the list. If we do not want to acquire/retrieve an object, only return null as result.
     * @param string $key Id of the item to be acquired.
     * @return INabuDataReadable|null Returns a @see { INabuDataReadable } instance if acquired or null if not.
     */
    abstract protected function acquireItem($key): ?INabuDataReadable;
    /**
     * This method is called internally by mergeArray() for each array item that we need to merge.
     * @param array $data Data array to pass to new instance.
     * @return INabuDataReadable|null Returns the new created instance if allowed or null otherwise.
     */
    abstract protected function createDataInstance(array $data): ?INabuDataReadable;

    public function __construct(?string $index_field = null, $source_list = null)
    {
        parent::__construct();

        $this->index_field = $index_field;
        $this->rewind();

        if ($source_list instanceof INabuDataList) {
            $this->merge($source_list);
        } elseif (is_array($source_list)) {
            $this->mergeArray($source_list);
        } elseif (!is_null($source_list)) {
            throw new InvalidArgumentException(sprintf(TRIGGER_ERROR_INVALID_ARGUMENT, '$source_list'));
        }
    }

    public function clear(): INabuDataIterable
    {
        $this->clearInternal();

        return $this;
    }

    public function getMainIndexFieldName(): ?string
    {
        return $this->index_field;
    }

    public function getKeys(string $index = null): ?array
    {
        return is_array($this->data) ? array_keys($this->data) : null;
    }

    public function getItems(): ?array
    {
        return $this->data;
    }

    public function hasKey($key, ?string $index = null): bool
    {
        $retval = false;

        if (is_scalar($key)) {
            $retval = is_array($this->data) && array_key_exists($key, $this->data);
        } else {
            trigger_error(sprintf(TRIGGER_ERROR_INVALID_KEY, var_export($key, true)));
        }

        return $retval;
    }

    public function addItem(INabuDataReadable $item, $key = null): INabuDataReadable
    {
        $retval = null;

        if (is_null($this->index_field)) {
            if (is_null($key)) {
                trigger_error(sprintf(TRIGGER_ERROR_INVALID_ARGUMENT, '$key'));
            }
            if (is_array($this->data)) {
                $this->data[$key] = $item;
                $retval = $item;
            } else {
                $this->data = array($key => $item);
                $retval = $item;
            }
        } else {
            if ($item->hasValue($this->index_field)) {
                if (is_array($this->data)) {
                    $this->data[$item->getValue($this->index_field)] = $item;
                    $retval = $item;
                } else {
                    $this->data = array(
                        $item->getValue($this->index_field) => $item
                    );
                    $retval = $item;
                }
            }
        }

        return $retval;
    }

    public function getItem(string $key, ?string $index = null): ?INabuDataReadable
    {
        $retval = null;

        if ($this->isFilled() && array_key_exists($key, $this->data)) {
            $retval = $this->data[$key];
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
                $retval = $this->data[$item];
                unset($this->data[$item]);
            } elseif ($item instanceof INabuDataReadable && in_array($item, $this->data)) {
                $keys = array_keys($this->data, $item, true);
                foreach ($keys as $key) {
                    unset($this->data[$key]);
                }
                $retval = $item;
            }
        } else {
            $nb_index_id = nb_getMixedValue($item, $this->index_field);
            if (!is_null($nb_index_id) && $this->hasKey($nb_index_id)) {
                $retval = $this->data[$nb_index_id];
                unset($this->data[$nb_index_id]);
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
                    $this->addItem($item, $key);
                    $count++;
                }
            }
        } elseif ($this->isEmpty() && $list->isFilled()) {
            $this->data = $list->data;
            $count = is_array($this->data) ? count($this->data) : 0;
         }

        return $count;
    }

    public function mergeArray(?array $array): int
    {
        $count = 0;

        if (is_array($array) && count($array) > 0) {
            foreach ($array as $key => $item) {
                $final_key = $this->locateKey($item, $key);
                if (!$this->hasKey($final_key)) {
                    if ($item instanceof INabuDataReadable) {
                        $this->addItem($item, $final_key);
                        $count++;
                    } elseif (is_array($item) &&
                              $this->addItem($this->createDataInstance($item), $final_key) instanceof INabuDataReadable
                    ) {
                        $count++;
                    } else {
                        throw new UnexpectedValueException();
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Locate the effective Key value for an item in mergeArray iterator.
     * @param mixed|null $item Item object. Can be an array or an instance of INabuDataReadable.
     * @param mixed|null $default Default value if index value does not exists or index_field is not setted.
     * @return mixed Returns the effective key.
     */
    private function locateKey($item = null, $default = null)
    {
        $value = $default;

        if (is_string($this->index_field)) {
            if ($item instanceof INabuDataReadable) {
                $value = $item->getValue($this->index_field);
            } elseif (is_array($item)) {
                if (array_key_exists($this->index_field, $item)) {
                    $value = $item[$this->index_field];
                }
            } else {
                throw new UnexpectedValueException();
            }
        }

        return $value;
    }
}
