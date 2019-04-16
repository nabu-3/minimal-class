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
 * Trait to manage a TNabuDataObject as a JSON object.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
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
     * Gets a value using a point style JSON path.
     * @param string $path The path of the value to locate.
     * @return mixed|null Returns the value in path if exists, or null otherwise.
     */
    public function getJSONValue(string $path)
    {
        $retval = null;

        if (!$this->isEmpty() &&
            mb_strlen($path) > 0 &&
            ($l = count($route = preg_split('/\./', $path))) > 0
        ) {
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if ($p !==null && is_array($p) && array_key_exists($route[$i], $p)) {
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
     * Check if a JSON Path exists.
     * @param string $path Path to check.
     * @return bool Returns true if the path exists.
     */
    public function checkJSONPath(string $path): bool
    {
        $retval = false;

        if (!$this->isEmpty() && mb_strlen($path) > 0) {
            $route = preg_split('/\./', $path);

            if (count($route) > 0) {
                $l = count($route);
                $p = &$this->data;
                for ($i = 0; $i < $l; $i++) {
                    if (!is_array($p) || !array_key_exists($route[$i], $p)) {
                        break;
                    }
                    $p = &$p[$route[$i]];
                }
                $retval = ($i === $l);
            }
        }

        return $retval;
    }

    public function isJSONValueEqualTo($path, $value)
    {
        if ($this->isEmpty() || mb_strlen($path) === 0) {
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
        if (mb_strlen($path) === 0) {
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
                    if ($flags & TRAIT_JSON_GRANT_PATH) {
                        $p[$route[$i]] = array();
                    } else {
                        return;
                    }
                }
            } elseif ($p === null && ($flags & TRAIT_JSON_GRANT_PATH)) {
                $p = array($route[$i] => null);
            } elseif ($flags & TRAIT_JSON_REPLACE_EXISTING) {
                $p = array($route[$i] => null);
            } else {
                return;
            }
            $p = &$p[$route[$i]];
        }
        $p = $value;
    }
}
