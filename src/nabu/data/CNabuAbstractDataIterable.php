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

use OutOfBoundsException;

use nabu\data\interfaces\INabuDataIterable;

use nabu\min\CNabuObject;

/**
 * Abstract class to implement iterable collections in nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.4
 * @version 3.0.4
 * @package \nabu\data
 */
abstract class CNabuAbstractDataIterable extends CNabuObject implements INabuDataIterable
{
    /** @var array|null Data stored in the instance. Functions that applies to set data in this instance modifies this
     * array. */
    protected $data = null;
    /** @var int Current Iterator position. */
    private $data_iteration = 0;

    public function __construct(array $data = null)
    {
        parent::__construct();

        $this->data = $data;
    }

    public function count(): int
    {
        return is_array($this->data) ? count($this->data) : 0;
    }

    public function isEmpty(): bool
    {
        return ($this->data === null) || (count($this->data) == 0);
    }

    public function isFilled(): bool
    {
        return is_array($this->data) && count($this->data) > 0;
    }

    public function current()
    {
        $current = null;

        if ($this->valid()) {
            $keys = array_keys($this->data);
            $current = $this->data[$keys[$this->data_iteration]];
        }

        return $current;
    }

    public function next()
    {
        $this->data_iteration++;
    }

    public function key()
    {
        $key = null;

        if ($this->valid()) {
            $keys = array_keys($this->data);
            $key = $keys[$this->data_iteration];
        }

        return $key;
    }

    public function valid()
    {
        return $this->data_iteration < $this->count();
    }

    public function rewind()
    {
        $this->data_iteration = 0;
    }

    public function seek($position)
    {
        $this->data_iteration = $position;

        if (!$this->valid()) {
            throw new OutOfBoundsException();
        }

        return $this->current();
    }

    /**
     * Internal method to clear collection at this level.
     */
    protected function clearInternal(): void
    {
        $this->data = null;
        $this->rewind();
    }
}
