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

use CNabuDataObject;

/**
 * Trait to manage a TNabuDataObject as a JSON object.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package \nabu\data\traits
 */
trait TNabuJSONData
{
    /** @var int JSON_GRANT_PATH Flag to determine if the path needs to be granted in a JSON node. */
    const JSON_GRANT_PATH = 0x0001;
    /** @var int JSON_REPLACE_EXISTING Flag to determine if an operation with JSON needs to replace existing path. */
    const JSON_REPLACE_EXISTING = 0x0002;

    public function checkJSONPath($path)
    {
        if ($this->isEmpty() || strlen($path) === 0) {
            return false;
        }

        $route = preg_split('/\./', $path);

        if (count($route) > 0) {
            $l = count($route);
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if (!is_array($p) || !array_key_exists($route[$i], $p)) {
                    return false;
                }
                $p = &$p[$route[$i]];
            }
            return true;
        } else {
            return false;
        }
    }

    public function getJSONValue($path)
    {
        if ($this->isEmpty() || strlen($path) === 0) {
            return false;
        }

        $route = preg_split('/\./', $path);

        if (count($route) > 0) {
            $l = count($route);
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if ($p ===null || !array_key_exists($route[$i], $p)) {
                    return false;
                }
                $p = &$p[$route[$i]];
            }
            return $p;
        } else {
            return false;
        }
    }

    public function isJSONValueEqualThan($path, $value)
    {
        if ($this->isEmpty() || strlen($path) === 0) {
            return false;
        }

        $route = preg_split('/\./', $path);

        if (count($route) > 0) {
            $l = count($route);
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if (!array_key_exists($route[$i], $p)) {
                    return false;
                }
                $p = &$p[$route[$i]];
            }
            return ($p === $value);
        } else {
            return false;
        }
    }

    public function setJSONValue($path, $value, $flags = 0)
    {
        if (strlen($path) === 0) {
            return false;
        }

        $route = preg_split('/\./', $path);

        if (count($route) === 0) {
            return;
        }

        $l = count($route);
        $p = &$this->data;
        for ($i = 0; $i < $l; $i++) {
            if (is_array($p)) {
                if (!array_key_exists($route[$i], $p)) {
                    if ($flags & CNabuDataObject::JSON_GRANT_PATH) {
                        $p[$route[$i]] = array();
                    } else {
                        return;
                    }
                }
            } elseif ($p === null && ($flags & CNabuDataObject::JSON_GRANT_PATH)) {
                $p = array($route[$i] => null);
            } elseif ($flags & CNabuDataObject::JSON_REPLACE_EXISTING) {
                $p = array($route[$i] => null);
            } else {
                return;
            }
            $p = &$p[$route[$i]];
        }
        $p = $value;
    }

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

        if ($value !== null && is_string($value)) {
            $value = json_decode($value, true);
        }

        return $value;
    }

    /**
     * Stores a value as a JSON encoded string.
     * @param string $name Name of the value to set.
     * @param mixed|null $value Value to be setted.
     * @return CNabuDataObject Returns self pointer for convenience for cascade calls.
     */
    public function setValueAsJSONEncoded(string $name, $value = null): CNabuDataObject
    {
        if ($value === null) {
            $this->setValue($name, null);
        } elseif (is_string($value)) {
            $this->setValue($name, $value);
        } elseif (is_array($value) || is_object($value)) {
            $this->setValue($name, json_encode($value));
        }

        return $this;
    }
}
