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

namespace nabu\data\traits;

/**
 * Trait to manage a TNabuRODataObject as iterable.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.4
 * @package \nabu\data\traits
 */
trait TNabuDataIterator
{
    /** @var int Current Iterator position. */
    private $data_position = 0;

    /**
     * Returns the current value at Iterator cursor position.
     * @return mixed|null Return the current value if exists or null otherwise.
     */
    public function current()
    {
        $current = null;

        if (is_array($iterdata = $this->getValuesAsArray()) &&
            count($iterdata) > $this->data_position
        ) {
            $keys = array_keys($iterdata);
            $current = $this->getValue($keys[$this->data_position]);
        }

        return $current;
    }

    /**
     * Increments the iterator cursor +1.
     */
    public function next()
    {
        $this->data_position++;
    }

    /**
     * Gets the current key value.
     * @return string|null Returns the key value if exists or null otherwise.
     */
    public function key()
    {
        $key = null;

        if (is_array($iterdata = $this->getValuesAsArray()) &&
            count($iterdata) > $this->data_position
        ) {
            $keys = array_keys($iterdata);
            $key = $keys[$this->data_position];
        }

        return $key;
    }

    /**
     * Check if the current position of the Iterator Cursor is valid.
     * @return bool Returns true if current position is valid.
     */
    public function valid()
    {
        $size = is_array($iterdata = $this->getValuesAsArray()) ? count($iterdata) : 0;

        return $this->data_position < $size;
    }

    /**
     * Rewind the Cursor position of the Iterator.
     */
    public function rewind()
    {
        $this->data_position = 0;
    }
}
