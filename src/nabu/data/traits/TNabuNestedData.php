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
 * Trait to manage a @see { TNabuDataObject } or a @see { TNabuRODataObject } as a nested or multilevel data.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package \nabu\data\traits
 */
trait TNabuNestedData
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
     * @return CNabuDataObject Returns self pointer for convenience for cascade calls.
     */
    public function setValueAsJSONEncoded(string $name, $value = null): CNabuDataObject
    {
        if ($this instanceof CNabuDataObject && $this->isEditable()) {
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
     * Gets a value using a point style JSON path. Overrides parent method.
     * @param string $path The path of the value to locate.
     * @return mixed|null Returns the value in path if exists, or null otherwise.
     */
    public function getValue(string $path)
    {
        $retval = null;

        if (!$this->isEmpty() &&
            mb_strlen($path) > 0 &&
            ($l = count($route = preg_split('/\./', $path))) > 0
        ) {
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if (is_array($p) && array_key_exists($route[$i], $p)) {
                    $p = &$p[$route[$i]];
                } else {
                    break;
                }
            }
            $retval = ($i === $l ? $p : null);
        }

        return $retval;
    }

    /**
     * Check if a Path exists.
     * @param string $path Path to check.
     * @return bool Returns true if the path exists.
     */
    public function checkPath(string $path): bool
    {
        $retval = false;

        if (!$this->isEmpty() &&
            mb_strlen($path) > 0 &&
            ($l = count($route = preg_split('/\./', $path))) > 0
        ) {
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if (is_array($p) && array_key_exists($route[$i], $p)) {
                    $p = &$p[$route[$i]];
                } else {
                    break;
                }
            }
            $retval = ($i === $l);
        }

        return $retval;
    }

    /**
     * Set a Nested data value.
     * @param string $path Path of the value to be setted.
     * @param mixed|null $value Value to be setted.
     * @param int $flags Flags to modify behavior of this method.
     * @return CNabuDataObject Returns self pointer for convenience.
     */
    public function setValue(string $path, $value = null, int $flags = TRAIT_NESTED_GRANT_PATH): CNabuDataObject
    {
        if ($this instanceof CNabuDataObject && $this->isEditable()) {
            if (mb_strlen($path) > 0 && ($l = count($route = preg_split('/\./', $path))) > 0) {
                $p = &$this->data;
                for ($i = 0; $i < $l; $i++) {
                    if (is_array($p)) {
                        if (!array_key_exists($route[$i], $p)) {
                            if ($flags & TRAIT_NESTED_GRANT_PATH) {
                                $p[$route[$i]] = null;
                            } else {
                                break;
                            }
                        }
                    } elseif (($p === null && ($flags & TRAIT_NESTED_GRANT_PATH)) || $flags & TRAIT_NESTED_REPLACE_EXISTING) {
                        $p = array($route[$i] => null);
                    } else {
                        break;
                    }
                    $p = &$p[$route[$i]];
                }
                if ($i === $l) {
                    $p = $value;
                }
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }
}
