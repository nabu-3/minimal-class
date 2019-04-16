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

use nabu\data\CNabuDataObject;

/**
 * Trait to manage a TNabuDataObject as a LIFO stack data object.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package \nabu\data\traits
 */
trait TNabuHistoryData
{
    /** @var array|null Data LIFO point of time stack. It can be used to make differential updates of data storages. */
    protected $data_stack = null;

    /**
     * Reset the data content stored in the instance and empty internal storage, lossing all previous stored data.
     * @return bool Returns true if the instance is reseted.
     */
    public function resetHistory(): bool
    {
        $retval = false;

        if ($this instanceof CNabuDataObject && $this->isEditable()) {
            parent::reset();
            if (is_array($this->data_stack)) {
                $this->data_stack = null;
                $retval = true;
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $retval;
    }

    /**
     * Check if the stack is empty or have history.
     * @return bool If the stack is empty returns true.
     */
    public function isHistoryEmpty(): bool
    {
        return $this->data_stack === null || count($this->data_stack) === 0;
    }

    /**
     * Copy current data to a point of time storage. This is a LIFO stack.
     * @return bool Returns true if they have data to be pushed.
     */
    public function push(): bool
    {
        $retval = false;

        if ($this instanceof CNabuDataObject && $this->isEditable()) {
            if (is_array($this->data)) {
                if (is_array($this->data_stack)) {
                    array_push($this->data_stack, $this->data);
                } else {
                    $this->data_stack = array($this->data);
                }
                $retval = true;
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $retval;
    }

    /**
     * Sets current data from the LIFO stack.
     * @return bool Returns true if the data is popped, and false if the LIFO is empty.
     */
    public function pop(): bool
    {
        $result = false;

        if ($this instanceof CNabuDataObject && $this->isEditable()) {
            if (is_array($this->data_stack) && count($this->data_stack)) {
                $this->data = array_pop($this->data_stack);
                $result = true;
            } else {
                $this->data = null;
                trigger_error("Data stack is empty. Current data is emptied.", E_USER_NOTICE);
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $result;
    }

    /**
     * Overwrite last LIFO Stack pushed data with current data if LIFO is filled, or pushes current data if is empty.
     * @return bool Returns true if data is overwrited or pushed.
     */
    public function overwrite(): bool
    {
        $retval = false;

        if ($this instanceof CNabuDataObject && $this->isEditable()) {
            if (is_array($this->data)) {
                if (is_array($this->data_stack)) {
                    array_pop($this->data_stack);
                }
                array_push($this->data_stack, $this->data);
                $retval = true;
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $retval;
    }

    /**
     * Check if a value is new or is previously stored in the data history stack.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value is new.
     */
    public function isValueNew(string $name): bool
    {
        $retval = false;

        if ($this->hasValue($name) && !$this->isHistoryEmpty()) {
            $pos = count($this->data_stack) - 1;
            $retval = !array_key_exists($name, $this->data_stack[$pos]);
        }

        return $retval;
    }

    /**
     * Check if a value was modified from last pushed data in the history stack.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the stack contains a previous stored data value and both values are differents.
     */
    public function isValueModified(string $name): bool
    {
        $retval = false;

        if ($this->hasValue($name) && !$this->isHistoryEmpty()) {
            $pos = count($this->data_stack) - 1;
            $retval = array_key_exists($name, $this->data_stack[$pos]) &&
                      $this->data[$name] !== $this->data_stack[$pos][$name]
            ;
        }

        return $retval;
    }
}
