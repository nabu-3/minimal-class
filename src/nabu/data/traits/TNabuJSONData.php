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

use nabu\data\interfaces\INabuDataReadable;
use nabu\data\interfaces\INabuDataWritable;

/**
 * Trait to manage a @see { INabuDataWritable } or a @see { INabuDataReadable } derived classes as JSON data.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.3
 * @version 3.0.4
 * @package \nabu\data\traits
 */
trait TNabuJSONData
{
    /**
     * Try to parse value as a JSON data descriptor. If success returns an array with parsed structure. If fails,
     * then returns the original value.
     * @param string $name Name of the value to get.
     * @return array|null Returns the array as result of decode the JSON, the original value if cannot be decoded,
     * or null if data value is empty or not exists.
     */
    public function getValueAsJSONDecoded(string $name)
    {
        $value = $this->getValue($name);

        if ($value !== null &&
            is_string($value) &&
            is_null($json = json_decode($value, true))
        ) {
            $json = array($value);
        }
        if (isset($json)) {
            $value = $json;
        }

        return $value;
    }

    /**
     * Stores a value as a JSON encoded string.
     * @param string $name Name of the value to set.
     * @param mixed|null $value Value to be setted.
     * @return INabuDataWritable Returns self pointer for convenience for cascade calls.
     */
    public function setValueAsJSONEncoded(string $name, $value = null): INabuDataWritable
    {
        if ($this instanceof INabuDataWritable && $this->isEditable()) {
            if ($value === null) {
                $this->setValue($name, null);
            } elseif (is_string($value)) {
                $this->setValue($name, $value);
            } elseif (is_array($value) || is_object($value)) {
                $this->setValue($name, json_encode($value));
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }

    /**
     * Converts internal data fo JSON string.
     * @return string|null If some data exists, then returns a string representation of JSON. Otherwise return null.
     */
    public function toJSON()
    {
        $result = null;

        if ($this instanceof INabuDataReadable) {
            $data = $this->getValuesAsArray();
            if (is_array($data)) {
                $result = json_encode($data, JSON_OBJECT_AS_ARRAY);
            }
        }

        return $result;
    }
}
